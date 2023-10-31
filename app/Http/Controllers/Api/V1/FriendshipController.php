<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FriendshipRequest\FriendRequestRespondRequest;
use App\Http\Requests\FriendshipRequest\FriendRequestSendRequest;
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

        $friends = Friendship::with('user')->where(function ($query) use ($userId) {
            $query->where('user1_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user2_id', $userId);
        })->get();

        dd($friends);
        $query = QueryBuilder::for(Friendship::class)
            ->allowedFilters([
                AllowedFilter::scope('sender_id'),
                AllowedFilter::scope('receiver_id'),
                'status',
            ])
            ->allowedSorts('created_at');

        $friendRequests = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new FriendshipRequestCollection($friendRequests);
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

        $friendRequest = FriendRequest::where('uuid', $requestId)->first();

        if ($friendRequest->receiver_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized to respond to this request.'], 403);
        }

        if ($response === 'accepted') {
            $friendRequest->status = 'accepted';
            $friendRequest->save();

            // Create a friendship between the users
            $this->createFriendship($friendRequest->sender_id, $friendRequest->receiver_id);

            try {
                $sender = $friendRequest->sender;
                SendEmailJob::dispatch([
                    'receiver' => $sender,
                    'data' => [
                        'recipient' => $sender->first_name, // the person who sent the friend request
                        'member' => $friendRequest->receiver->first_name, //the person who was invited
                    ],
                ], 'friendship_request_accepted');
            } catch (\Exception $e) {
                throw new ApiException($e, 'CS-01');
            }

        } else {
            $friendRequest->status = 'declined';
            $friendRequest->save();
        }

        return response()->json(['message' => 'Friend request responded.']);
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
        return FriendRequest::where(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        })
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();
    }

    private function createFriendship($user1Id, $user2Id)
    {
        DB::table('friendships')->insert([
            ['user1_id' => $user1Id, 'user2_id' => $user2Id],
        ]);
    }
}
