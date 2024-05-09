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
        $sender = $request?->sender_id ? User::where('uuid', $request->sender_id)->first() : $request->user();
        $receiver = User::where('uuid', $request->receiver_id)->first();

        // Check if a friendship already exists or a pending request
        $existingFriendship = $this->checkExistingFriendship($sender->id, $receiver->id);
        if (!$existingFriendship) {
            // Create a new friend request
            FriendRequest::create([
                'uuid' => Str::uuid(),
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'status' => 'pending',
            ]);

            try {
                if ($sender->role == 'creative') {
                    $profile_url = '/creative/' . $sender->creative?->slug ?? '';
                } elseif ($sender->role == 'agency') {
                    $profile_url = '/agency/' . $sender->agency?->slug ?? '';
                } else {
                    $profile_url = $sender->username;
                }
                // SendEmailJob::dispatch([
                //     'receiver' => $receiver,
                //     'data' => [
                //         'recipient' => $receiver->first_name,
                //         'inviter' => $sender->first_name,
                //         'iniviter_profile' => sprintf("%s%s", env('FRONTEND_URL'), $profile_url),
                //     ],
                // ], 'friendship_request_sent');
            } catch (\Exception $e) {
                throw new ApiException($e, 'CS-01');
            }

            return response()->json(['message' => 'Friend request sent.']);
        } else {

            if ($existingFriendship->status == 'pending') {
                if ($existingFriendship->sender_id == $sender->id) {
                    return response()->json(['message' => 'Friendship pending request already exists.'], 400);
                } else {
                    //make the friends
                    $existingFriendship->update(['status' => 'accepted']);
                    // Create a friendship between the users
                    $this->createFriendship($existingFriendship->sender_id, $existingFriendship->receiver_id);

                    return response()->json(['message' => 'You both are now friends.'], 200);
                }
            } elseif ($existingFriendship->status == 'accepted') {
                return response()->json(['message' => 'Friendship already exists.'], 400);
            } elseif ($existingFriendship->status === 'cancelled' || $existingFriendship->status === 'declined') {
                $existingFriendship->update(['status' => 'pending']);

                return response()->json(['message' => 'Friendship request sent again.']);
            } elseif ($existingFriendship->status == 'unfriended') {
                $existingFriendship->update([
                    'status' => 'pending',
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                ]);

                return response()->json(['message' => 'Friendship request sent again.']);
            }
        }
    }

    public function respondToFriendRequest(FriendRequestRespondRequest $request)
    {
        $user = $request->user();
        $requestId = $request->input('request_id');
        $response = $request->input('response');

        // Fetch the friend request
        $friendRequest = FriendRequest::where('uuid', $requestId)->first();

        // if (!$friendRequest) {
        //     return response()->json(['message' => 'This request has already been responded.'], 403);
        // }

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
                // SendEmailJob::dispatch([
                //     'receiver' => $friendRequest->sender,
                //     'data' => [
                //         'recipient' => $friendRequest->sender->first_name,
                //         'member' => $friendRequest->receiver->first_name,
                //     ],
                // ], 'friendship_request_accepted');
            } elseif ($response === 'cancelled') {
                $friendRequest->update(['status' => 'cancelled']);
            } elseif ($response === 'declined') {
                $friendRequest->update(['status' => 'declined']);
            } elseif ($response === 'unfriended') {
                $friendRequest->update(['status' => 'unfriended']);
                $this->deleteFriendship($friendRequest->sender_id, $friendRequest->receiver_id);
                DB::commit(); //commit the transaction because in this case we are not using the default resposne message

                return response()->json(['message' => sprintf('You both are no longer friends.')]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiException($e, 'CS-01');
        }

        return response()->json(['message' => sprintf('Friend request %s', $response)]);
    }

    public function getFriends()
    {
        $user = auth()->user();
        $friends = $user->friends;

        return response()->json($friends);
    }

    // Helper methods
    private function checkExistingFriendship($user1Id, $user2Id)
    {
        return FriendRequest::where(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        })
            ->first();
    }

    private function createFriendship($user1Id, $user2Id)
    {
        Friendship::updateOrCreate(
            [
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
            ]
        );
    }

    //create funciton for unfriend user
    public function unfriend(Request $request)
    {
        $user = $request->user();
        $friend = User::where('uuid', $request->friend_id)->first();

        $friendship = Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user1_id', $user->id)->where('user2_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user1_id', $friend->id)->where('user2_id', $user->id);
        })->first();

        if (!$friendship) {
            return response()->json(['message' => 'Friendship does not exist.'], 400);
        }

        $friendship->delete();

        return response()->json(['message' => 'Friendship deleted.']);
    }

    public function deleteFriendship($user1Id, $user2Id)
    {
        $friendship = Friendship::where(function ($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user1Id)->where('user2_id', $user2Id);
        })->orWhere(function ($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user2Id)->where('user2_id', $user1Id);
        })->first();

        $friendship->delete();

        // Friend request will be updated to unfriended in Friendship Model delete event
    }
}