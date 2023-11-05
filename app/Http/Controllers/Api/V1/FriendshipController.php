<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FriendshipRequest\FriendRequestRespondRequest;
use App\Http\Requests\FriendshipRequest\FriendRequestSendRequest;
use App\Http\Resources\Friendship\FriendshipCollection;
use App\Http\Resources\Friendship\FriendshipRequestCollection;
use App\Jobs\SendEmailJob;
use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class FriendshipController extends Controller
{
    public function all_friends(Request $request)
    {
        $userId = $request->user()->id;

        $friends = Friendship::with('initiatedByUser', 'receivedByUser')->where(function ($query) use ($userId) {
            $query->where('user1_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user2_id', $userId);
        })->get();

        return new FriendshipCollection($friends);
    }

    public function index(Request $request)
    {
        $query = QueryBuilder::for(FriendRequest::class)
            ->allowedFilters([
                AllowedFilter::scope('sender_id'),
                AllowedFilter::scope('receiver_id'),
                'status',
            ])
            ->allowedSorts('created_at');

        $friendRequests = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new FriendshipRequestCollection($friendRequests);
    }

    public function sendFriendRequest(FriendRequestSendRequest $request)
    {
        // FriendRequest::where('id', '>', 0)->delete();
        $sender = $request->user();
        $receiver = User::where('uuid', $request->receiver_id)->first();

        // Check if a friendship already exists or a pending request
        if ($this->checkFriendshipExists($sender->id, $receiver->id)) {
            return response()->json(['message' => 'Friendship or pending request already exists.'], 400);
        }

        // Create a new friend request
        FriendRequest::create([
            'uuid' => Str::uuid(),
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        try {
            if ($sender->role == 'creative') {
                $profile_url = '/creative/'.$sender->creative?->slug ?? '';
            } elseif ($sender->role == 'agency') {
                $profile_url = '/agency/'.$sender->agency?->slug ?? '';
            } else {
                $profile_url = $sender->username;
            }
            SendEmailJob::dispatch([
                'receiver' => $receiver,
                'data' => [
                    'recipient' => $receiver->first_name,
                    'inviter' => $sender->first_name,
                    'iniviter_profile' => env('FRONTEND_URL').$profile_url,
                ],
            ], 'friendship_request_sent');
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }

        return response()->json(['message' => 'Friend request sent.']);
    }

    public function respondToFriendRequest(FriendRequestRespondRequest $request)
    {
        $user = $request->user();
        $requestId = $request->input('request_id');
        $response = $request->input('response');

        // Fetch the friend request
        $friendRequest = FriendRequest::where('uuid', $requestId)
            ->where('status', 'pending')
            ->first();

        if (! $friendRequest) {
            return response()->json(['message' => 'This request has already been responded.'], 403);
        }

        // if ($friendRequest->receiver_id !== $user->id) {
        //     return response()->json(['message' => 'Unauthorized to respond to this request.'], 403);
        // }

        DB::beginTransaction();
        try {
            if ($response === 'accepted') {
                $friendRequest->update(['status' => 'accepted']);

                // Create a friendship between the users
                $this->createFriendship($friendRequest->sender_id, $friendRequest->receiver_id);

                // Dispatch email job
                SendEmailJob::dispatch([
                    'receiver' => $friendRequest->sender,
                    'data' => [
                        'recipient' => $friendRequest->sender->first_name,
                        'member' => $friendRequest->receiver->first_name,
                    ],
                ], 'friendship_request_accepted');
            } elseif($response === 'cancelled')
            {
                $friendRequest->update(['status' => 'cancelled']);
            }
            elseif($response === 'declined')
            {
                $friendRequest->update(['status' => 'declined']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiException($e, 'CS-01');
        }

            return response()->json(['message' => sprintf("Friend request %s", $response)]);
    }

    public function getFriends()
    {
        $user = auth()->user();
        $friends = $user->friends;

        return response()->json($friends);
    }

    // Helper methods
    private function checkFriendshipExists($user1Id, $user2Id)
    {
        return FriendRequest::where('status', 'pending')
        ->where(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        })
        ->exists();
    }

    private function createFriendship($user1Id, $user2Id)
    {
        DB::table('friendships')->insert([
            [
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
