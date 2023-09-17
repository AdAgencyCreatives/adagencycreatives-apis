<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\MessageReceived;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\Message\MessageCollection;
use App\Http\Resources\Message\MessageResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index(Request $request, $contactId)
    {

        $contact = User::where('uuid', $contactId)->firstOrFail();
        $contact_id = $contact->id;
        $userId = request()->user()->id;

        $messages = Message::where(function ($query) use ($userId, $contact_id) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $contact_id);
        })
            ->orWhere(function ($query) use ($userId, $contact_id) {
                $query->where('sender_id', $contact_id)
                    ->where('receiver_id', $userId);
            })
            ->latest()
                        //    ->toSql();
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        //    dd($messages);
        return new MessageCollection($messages, $userId);
    }

    public function store(StoreMessageRequest $request)
    {
        try {
            $sender = User::where('uuid', $request->sender_id)->first();
            $receiver = User::where('uuid', $request->receiver_id)->first();
            $event_data = [
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'type' => 'received',
            ];
            $request->merge([
                'uuid' => Str::uuid(),
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => $request->message,
            ]);

            $message = Message::create($request->all());
            $msg_resource = new MessageResource($message);
            event(new MessageReceived($event_data));

            return $msg_resource;
        } catch (\Exception $e) {
            throw new ApiException($e, 'MS-01');
        }
    }

    public function getAllMessageContacts()
    {
        $userId = request()->user()->id;

        $contacts = Message::select('sender_id', 'receiver_id', 'message')
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->distinct()
            ->select(
                \DB::raw('IF(sender_id = '.$userId.', receiver_id, sender_id) AS id'),
                \DB::raw('IF(sender_id = '.$userId.', (SELECT uuid FROM users WHERE id = receiver_id), (SELECT uuid FROM users WHERE id = sender_id)) AS uuid'),
                \DB::raw('IF(sender_id = '.$userId.', (SELECT first_name FROM users WHERE id = receiver_id), (SELECT first_name FROM users WHERE id = sender_id)) AS first_name'),
                \DB::raw('IF(sender_id = '.$userId.', (SELECT last_name FROM users WHERE id = receiver_id), (SELECT last_name FROM users WHERE id = sender_id)) AS last_name'),
            )
            ->get();

        return response()->json(['contacts' => $contacts]);
    }

    public function fetchMessages($contactId)
    {
        $loggedInUserId = 2;
        dd($loggedInUserId);
        $messages = Message::where(function ($query) use ($loggedInUserId, $receiverId) {
            $query->where('sender_id', $loggedInUserId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($loggedInUserId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $loggedInUserId);
        })->orderBy('created_at', 'asc')->get();

        return MessageResource::collection($messages);

    }
}
