<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleNotification\StoreScheduleNotificationRequest;
use App\Http\Resources\Schedule\ScheduleNotificationCollection;
use App\Http\Resources\Schedule\ScheduleNotificationResource;
use App\Models\Post;
use App\Models\ScheduleNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class ScheduleNotificationController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = QueryBuilder::for(ScheduleNotification::class)
            ->allowedFilters([
                AllowedFilter::scope('sender_id'),
                AllowedFilter::scope('recipient_id'),
                AllowedFilter::scope('post_id'),
                'status',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        $schedule_notifications = $query->with(['reactions' => function ($query) {
            // You can further customize the reactions query if needed
        }])
            ->whereHas('sender') // If the user is deleted, don't show the attachment
            ->whereHas('recipient') // If the user is deleted, don't show the attachment
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new ScheduleNotificationCollection($schedule_notifications);
    }

    public function store(StoreScheduleNotificationRequest $request)
    {
        try {
            $sender = User::where('uuid', $request->sender_id)->first();
            $recipients = User::whereIn('uuid', $request->recipient_id)->get();
            $post = Post::where('uuid', $request->post_id)->first();
            $data = [
                'uuid' => Str::uuid(),
                'sender_id' => $sender->id,
                'post_id' => $post->id,
                'status' => "0",
                'type' => $request->type,
                'notification_text' => $request->notification_text,
                'scheduled_at' => now()->addMinutes(15),
            ];
            $ids = [];
            if (!empty($recipients) && $recipients->count() > 0) {
                foreach ($recipients as $recipient) {
                    $data['recipient_id'] = $recipient->id;
                    $schedule_notification = ScheduleNotification::create($data);
                    $ids[] = $schedule_notification->id;
                }
                $schedule_notifications = ScheduleNotification::whereIn('id', $ids)->get();
                return ApiResponse::success(new ScheduleNotificationCollection($schedule_notifications), 200);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('PS-01' . $e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $schedule_notification = ScheduleNotification::where('uuid', $uuid)->firstOrFail();
            return new ScheduleNotificationResource($schedule_notification);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $schedule_notification = ScheduleNotification::where('uuid', $uuid)->firstOrFail();
            $schedule_notification->update($request->all());
            return new ScheduleNotificationResource($schedule_notification);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $schedule_notification = ScheduleNotification::where('uuid', $uuid)->firstOrFail();
            foreach ($schedule_notification->attachments as $attachment) {
                $attachment->delete();
            }
            $schedule_notification->delete();
            return ApiResponse::success(new ScheduleNotificationResource($schedule_notification), 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
