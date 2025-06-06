<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Resources\Notification\NotificationCollection;
use App\Http\Resources\Notification\NotificationResource;
use App\Jobs\SendEmailJob;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Notification::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::exact('type'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        if ($request->has('status')) {
            if ($request->status == 0) {
                $query->whereNull('read_at');
            } elseif ($request->status == 1) {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new NotificationCollection($notifications);
    }

    public function store(StoreNotificationRequest $request)
    {
        try {
            $user = User::where('uuid', $request->user_id)->first();

            $request->merge([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
            ]);

            if ($request->has('sender_id')) {
                $sender = User::where('uuid', $request->sender_id)->first();
                $request->merge(['sender_id' => $sender->id]);
            }

            if ($request->has('body')) {
                $body = $request->input('body');
                if (isset($body['post_id'])) {

                    $post = Post::where('uuid', $body['post_id'])->first();
                    $request->merge(['body' => $post->id]);
                }
            }

            $notification = Notification::create($request->all());

            return new NotificationResource($notification);
        } catch (\Exception $e) {
            throw new ApiException($e, 'NS-01');
        }
    }

    public function update($uuid)
    {
        Notification::where('uuid', $uuid)->touch('read_at');

        return response()->json([
            'message' => 'Notification updated successfully',
        ]);
    }

    public function destroy($uuid)
    {
        try {
            $notification = Notification::where('uuid', $uuid)->firstOrFail();
            $notification->delete();

            return ApiResponse::success($notification, 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function count(Request $request)
    {
        $query = QueryBuilder::for(Notification::class)
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

    public function sendLoungeMentionNotifications(Request $request)
    {
        try {

            $recipient_ids = $request?->recipient_ids;

            foreach ($recipient_ids as $recipient_id) {

                $user = User::where('uuid', $recipient_id)->first();

                $data = array();
                $data['uuid'] = Str::uuid();

                $data['user_id'] = $user->id;
                $data['type'] = $request->notification_type;
                $data['message'] = $request->notification_text;

                $post = Post::where('uuid', $request->post_id)->first();
                $data['body'] = array('post_id' => $post->id);

                $data['sender_id'] = $post->user->id;

                $notification = Notification::create($data);

                $group = $post->group;
                $receiver = User::find($notification->user_id);
                $author = $post->user;

                $group_url = $group ? ($group->slug == 'feed' ? env('FRONTEND_URL') . '/community' : env('FRONTEND_URL') . '/groups/' . $group->uuid) : '';

                $data = [
                    'data' => [
                        'recipient' => $receiver->first_name,
                        'name' => $author->full_name,
                        'inviter' => $author->full_name,
                        'inviter_profile_url' => sprintf('%s/creative/%s', env('FRONTEND_URL'), $author?->username),
                        'profile_picture' => get_profile_picture($author),
                        'user' => $author,
                        'group_url' => $group_url,
                        'group' => $group->name,
                        'post_time' => \Carbon\Carbon::parse($post->created_at)->diffForHumans(),
                        'notification_uuid' => $notification->uuid,
                    ],
                    'receiver' => $receiver
                ];

                if ($request?->send_email == 'yes') {
                    SendEmailJob::dispatch($data, 'user_mentioned_in_post');
                }
            }
        } catch (\Exception $e) {
            throw new ApiException($e, 'NS-01');
        }

        return json_encode(array('status' => 'success'));
    }
}