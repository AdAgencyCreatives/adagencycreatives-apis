<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\MessageReceived;
use App\Events\ConversationUpdated;
use App\Exceptions\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\Message\MessageCollection;
use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\User\UserResource;
use App\Jobs\SendEmailJob;
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
                    ->where('receiver_id', $contactId)
                    ->whereNull('sender_conversation_deleted_at');
            })->orWhere(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $contactId)
                    ->where('receiver_id', $userId)
                    ->whereNull('receiver_conversation_deleted_at');
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

            $event_data1 = [
                'receiver_id' => $request->receiver_id,
                'message_sender_id' => $request->sender_id,
                'message_receiver_id' => $request->receiver_id,
                'message' => $sender->full_name . ' sent a message to you',
                'message_type' => 'conversation_updated',
                'message_action' => 'message-received'
            ];

            $event_data2 = [
                'receiver_id' => $request->sender_id,
                'message_sender_id' => $request->sender_id,
                'message_receiver_id' => $request->receiver_id,
                'message' => 'You sent a message to ' . $receiver->full_name,
                'message_type' => 'conversation_updated',
                'message_action' => 'message-sent'
            ];


            $request->merge([
                'uuid' => Str::uuid(),
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => $request->message,
            ]);

            $update_data = ['read_at' => now()];


            //Mark previous messages as read
            Message::where('type', $type)
                ->where('receiver_id', $sender->id) // Only those messages in which I am receiver,
                ->where('sender_id', $receiver->id)
                ->whereNull('read_at')
                ->update($update_data);

            $message = Message::create($request->all());
            $msg_resource = new MessageResource($message, $sender->uuid);

            event(new MessageReceived($event_data1));
            event(new MessageReceived($event_data2));

            if ($message?->sender?->email_notifications_enabled) {

                $name = $message?->sender?->first_name . ' ' . $message?->sender?->last_name;
                $related = '';
                $recent_messages = [];

                if ($message?->sender?->agency) {
                    $name = $message?->sender?->agency->name;
                    // $related = $message?->sender?->first_name;
                } else if ($message?->sender?->creative) {
                    // $related = $message?->sender?->creative?->title;
                    if ($message?->sender?->creative?->category?->name) {
                        $related = $message?->sender?->creative?->category?->name;
                    }
                }

                $recent_messages[] = [
                    'name' => $name,
                    'profile_url' => env('FRONTEND_URL') . '/profile/' . $message->sender->id,
                    'profile_picture' => get_profile_picture($message->sender),
                    'message_time' => \Carbon\Carbon::parse($message->max_created_at)->diffForHumans(),
                    'related' => $related,
                ];

                $data = [
                    'recipient' => $message?->receiver?->first_name,
                    'unread_message_count' => 1,
                    'recent_messages' => $recent_messages,
                    'date_range' => now()
                ];

                SendEmailJob::dispatch([
                    'receiver' => $message?->receiver,
                    'data' => $data,
                ], 'unread_message');

                $event_data3 = [
                    'receiver_id' => $request->sender_id,
                    'message_sender_id' => $request->sender_id,
                    'message_receiver_id' => $request->receiver_id,
                    'message' => 'An email notification is sent to ' . $receiver->full_name,
                    'message_type' => 'conversation_updated',
                    'message_action' => 'message-sent'
                ];
                event(new MessageReceived($event_data3));
            }

            return $msg_resource;
        } catch (\Exception $e) {
            throw new ApiException($e, 'MS-01');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $message = Message::findOrFail($id);
            $sender = User::where('id', $message->sender_id)->firstOrFail();
            $receiver = User::where('id', $message->receiver_id)->firstOrFail();

            $event_data1 = [
                'receiver_id' => $receiver->uuid,
                'message_sender_id' => $sender->uuid,
                'message_receiver_id' => $receiver->uuid,
                'message' => $sender->full_name . ' updated a message sent to you',
                'message_type' => 'conversation_updated',
                'message_action' => 'message-updated'
            ];

            $event_data2 = [
                'receiver_id' => $sender->uuid,
                'message_sender_id' => $sender->uuid,
                'message_receiver_id' => $receiver->uuid,
                'message' => 'You edited a message sent to ' . $receiver->full_name,
                'message_type' => 'conversation_updated',
                'message_action' => 'message-edited'
            ];

            // Only update the message content
            $message->update([
                'message' => $request->message,
                'edited_at' => now()
            ]);

            event(new MessageReceived($event_data1));
            event(new MessageReceived($event_data2));

            return new MessageResource($message);
        } catch (\Exception $e) {
            throw new ApiException($e, 'MS-02');
        }
    }

    public function destroy($id)
    {
        try {
            $message = Message::where('id', $id)->firstOrFail();
            $sender = User::where('id', $message->sender_id)->firstOrFail();
            $receiver = User::where('id', $message->receiver_id)->firstOrFail();

            $event_data1 = [
                'sender_id' => $sender->uuid,
                'receiver_id' => $receiver->uuid,
                'message' => $sender->full_name . ' deleted a message sent to you',
                'message_type' => 'conversation_updated',
                'message_action' => 'message-deleted'
            ];

            $event_data2 = [
                'sender_id' => $receiver->uuid,
                'receiver_id' => $sender->uuid,
                'message' => 'You deleted a message sent to ' . $receiver->full_name,
                'message_type' => 'conversation_updated',
                'message_action' => 'message-deleted'
            ];

            $message->delete();

            event(new MessageReceived($event_data1));
            event(new MessageReceived($event_data2));

            return new MessageResource($message);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function deleteSingleMessage(Request $request, $id)
    {
        try {
            $auth_user = $request->user();
            $message = Message::where('id', $id)->firstOrFail();
            $sender = User::where('id', $message->sender_id)->firstOrFail();
            $receiver = User::where('id', $message->receiver_id)->firstOrFail();

            $is_sender = $auth_user->id == $sender->id;
            $is_receiver = $auth_user->id == $receiver->id;

            $event_data1 = [
                'receiver_id' => $sender->uuid,
                'message_sender_id' => $sender->uuid,
                'message_receiver_id' => $receiver->uuid,
                'message' => 'You deleted a message',
                'message_type' => 'conversation_updated',
                'message_action' => 'message-deleted'
            ];

            $event_data2 = [
                'receiver_id' => $receiver->uuid,
                'message_sender_id' => $sender->uuid,
                'message_receiver_id' => $receiver->uuid,
                'message' => $sender->full_name . ' deleted a message',
                'message_type' => 'conversation_updated',
                'message_action' => 'message-deleted'
            ];

            if (true || $is_sender) {
                $message->update([
                    'sender_deleted_at' => now()
                ]);
                event(new MessageReceived($event_data1));
            }

            if (true || $is_receiver) {
                $message->update([
                    'receiver_deleted_at' => now()
                ]);
                event(new MessageReceived($event_data2));
            }

            $message = Message::where('id', $id)->firstOrFail();

            return new MessageResource($message);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function getAllMessageContacts(Request $request) // Get list of contacts to be shown on left panel
    {
        $userId = request()->user()->id;

        // $contacts = Message::with('sender', 'receiver')->where(function ($query) use ($userId) {
        //     $query->where(function ($query) use ($userId) {
        //         $query->where('sender_id', $userId)
        //             ->whereNull('sender_conversation_deleted_at');
        //     })->orWhere(function ($query) use ($userId) {
        //         $query->where('receiver_id', $userId)
        //             ->whereNull('receiver_conversation_deleted_at');
        //     });
        // });

        // $contacts = Message::with('sender', 'receiver')
        //     ->whereNull('sender_deleted_at')
        //     ->WhereNull('receiver_deleted_at')
        //     ->whereNull('sender_conversation_deleted_at')
        //     ->WhereNull('receiver_conversation_deleted_at');

        $contacts = Message::with('sender', 'receiver')
            ->where(function ($query) use ($userId) {
                $query->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                        ->whereNull('sender_deleted_at')
                        ->whereNull('sender_conversation_deleted_at');
                })->orWhere(function ($query) use ($userId) {
                    $query->where('receiver_id', $userId)
                        ->whereNull('receiver_deleted_at')
                        ->whereNull('receiver_conversation_deleted_at');
                });
            });

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

    public function getSql($query)
    {
        $sql = $query->toSql();
        foreach ($query->getBindings() as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }

    public function deleteConversation(Request $request)
    {
        $auth_user = $request->user();
        $message_types = explode(',', $request->message_type);
        $counts = 0;

        $user1 = User::where('id', $request->user1)->firstOrFail();
        $user2 = User::where('id', $request->user2)->firstOrFail();

        $is_user1 = $auth_user->id == $user1->id;
        $is_user2 = $auth_user->id == $user2->id;

        $event_data1 = [
            'receiver_id' => $user1->uuid,
            'message_sender_id' => $user1->uuid,
            'message_receiver_id' => $user2->uuid,
            'message' => 'You deleted a conversation',
            'message_type' => 'conversation_updated',
            'message_action' => 'conversation-deleted'
        ];

        $event_data2 = [
            'receiver_id' => $user2->uuid,
            'message_sender_id' => $user1->uuid,
            'message_receiver_id' => $user2->uuid,
            'message' => 'You deleted a conversation',
            'message_type' => 'conversation_updated',
            'message_action' => 'conversation-deleted'
        ];

        foreach ($message_types as $message_type) {
            if ($is_user1) {
                $query1 = QueryBuilder::for(Message::class);
                $query1->whereRaw(
                    "type=? AND (sender_id=? and receiver_id=?)",
                    [$message_type, $request->user1, $request->user2]
                );

                $query2 = QueryBuilder::for(Message::class);
                $query2->whereRaw(
                    "type=? AND (sender_id=? and receiver_id=?)",
                    [$message_type, $request->user2, $request->user1]
                );
            } else {
                $query2 = QueryBuilder::for(Message::class);
                $query2->whereRaw(
                    "type=? AND (sender_id=? and receiver_id=?)",
                    [$message_type, $request->user1, $request->user2]
                );

                $query1 = QueryBuilder::for(Message::class);
                $query1->whereRaw(
                    "type=? AND (sender_id=? and receiver_id=?)",
                    [$message_type, $request->user2, $request->user1]
                );
            }


            $counts = $counts + $query1->update([
                'sender_conversation_deleted_at' => now()
            ]);
            $counts = $counts + $query2->update([
                'receiver_conversation_deleted_at' => now()
            ]);
        }

        if ($counts > 0) {
            if ($is_user1) {
                event(new MessageReceived($event_data1));
            }
            if ($is_user2) {
                event(new MessageReceived($event_data2));
            }
        }

        // return response()->json($this->getSql($query));
        return response()->json($counts);
    }
}