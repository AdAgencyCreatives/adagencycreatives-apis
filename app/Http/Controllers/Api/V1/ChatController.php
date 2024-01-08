<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\MessageReceived;
use App\Exceptions\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\Message\MessageCollection;
use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\User\UserResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ChatController extends Controller
{
    public function index(Request $request, $contactId)
    {
        $contact = User::where('uuid', $contactId)->firstOrFail();
        $contactId = $contact->id;

        $userId = request()->user()->id;

        $messages = Message::where(function ($query) use ($userId, $contactId) {
            $query->where(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $contactId);
            })->orWhere(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $contactId)
                    ->where('receiver_id', $userId);
            });
        });

        $types = [];
        // Add the dynamic type condition if provided in the request
        if ($request->has('type')) {
            $types = explode(',', $request->type);
            $messages->whereIn('type', $types);
        }

        $messages = $messages->latest()
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        // Read all messages between these two users
        Message::where('sender_id', $contactId)
            ->where('receiver_id', $userId)
            ->whereIn('type', $types)
            ->whereNull('read_at')
            ->touch('read_at');

        // Reverse the order of items in the resource collection
        $reversedMessages = new MessageCollection($messages->reverse());

        return $reversedMessages;
    }

    public function store(StoreMessageRequest $request)
    {
        try {
            $sender = User::where('uuid', $request->sender_id)->first();
            $receiver = User::where('uuid', $request->receiver_id)->first();
            $type = $request->type ?? 'private';

            $event_data = [
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'type' => $type,
                'message_type' => 'received',
                'user_name' => $sender->full_name,
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'human_readable_date' => now()->diffForHumans(),
            ];
            $request->merge([
                'uuid' => Str::uuid(),
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => $request->message,
            ]);

            //Mark previous messages as read
            Message::where('type', $type)
                ->where('receiver_id', $sender->id) // Only those messages in which I am receiver,
                ->where('sender_id', $receiver->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $message = Message::create($request->all());
            $msg_resource = new MessageResource($message, $sender->uuid);
            event(new MessageReceived($event_data));

            return $msg_resource;
        } catch (\Exception $e) {
            throw new ApiException($e, 'MS-01');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $message = Message::findOrFail($id);
            // Only update the message content
            $message->update(['message' => $request->message]);

            return new MessageResource($message);
        } catch (\Exception $e) {
            throw new ApiException($e, 'MS-02');
        }
    }

    public function destroy($id)
    {
        try {
            $message = Message::where('id', $id)->firstOrFail();
            $message->delete();

            return new MessageResource($message);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function getAllMessageContacts(Request $request) // Get list of contacts to be shown on left panel
    {
        $userId = request()->user()->id;

        $contacts = Message::with('sender', 'receiver');

        $types = [];
        // Add the dynamic type condition if provided in the request
        if ($request->has('type')) {
            $types = explode(',', $request->type);
            $contacts->whereIn('type', $types);
        }

        $contacts = $contacts->where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
        });

        $contacts = $contacts->latest()->get();

        $uniqueContacts = [];
        $uniquePairs = []; // To store unique pairs

        try {
            foreach ($contacts as $contact) {
                $senderId = $contact->sender_id;
                $receiverId = $contact->receiver_id;

                $sortedPair = [$senderId, $receiverId];
                sort($sortedPair);

                // Check if the reverse pair is already added
                if (!in_array($sortedPair, $uniquePairs)) {

                    if (!isset($contact->receiver)) {
                        continue;
                    }
                    if (!isset($contact->sender)) {
                        continue;
                    }

                    if ($senderId == $userId) {
                        $contact->message_type = 'sent';
                        unset($contact['sender']);
                        $contact->contact = new UserResource($contact->receiver);
                        unset($contact['receiver']);
                    } elseif ($receiverId == $userId) {
                        $contact->message_type = 'received';
                        unset($contact['receiver']);
                        $contact->contact = new UserResource($contact->sender);
                        unset($contact['sender']);
                    }

                    $uniquePairs[] = $sortedPair;
                    $uniqueContacts[] = $contact;
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->json(['contacts' => $uniqueContacts]);
    }

    private function getMessageType($sender1, $current_user)
    {
        return $sender1->uuid === $current_user->uuid ? 'sent' : 'received';
    }

    public function mark_as_read(Request $request, $sender_id)
    {
        $user = $request->user();
        $sender = User::where('uuid', $sender_id)->first();
        $msg_type = $request->type;

        Message::where('sender_id', $sender->id)
            ->where('receiver_id', $user->id)
            ->where('type', $msg_type)
            ->whereNull('read_at')
            ->touch('read_at');

        return response()->json(['success' => true], 200);
    }

    public function count(Request $request)
    {
        $query = QueryBuilder::for(Message::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::exact('type'),
            ]);

        if ($request->has('status')) {
            if ($request->status == 0) {
                $query->whereNull('read_at');
            } elseif ($request->status == 1) {
                $query->whereNotNull('read_at');
            }
        }

        return response()->json([
            'count' => $query->count(),
        ]);
    }

    public function deleteConversation(Request $request)
    {
        $query = QueryBuilder::for(Message::class);

        $query->whereRaw("type IN (?) AND ((sender_id=? and receiver_id=?) OR (sender_id=? and receiver_id=?))", [explode(",", $request->message_type), $request->user1, $request->user2, $request->user2, $request->user1]);

        return response()->json($query->get());
    }
}
