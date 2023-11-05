<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Requests\Notification\UpdateNotificationRequest;
use App\Http\Resources\Notification\NotificationCollection;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Notification::class)
        ->allowedFilters([
                AllowedFilter::scope('user_id')
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

        $notifications = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));
        return new NotificationCollection($notifications);
    }

    public function store(StoreNotificationRequest $request)
    {
        try {
            $user = User::where('uuid', $request->user_id)->first();

            $request->merge([
               'uuid' => Str::uuid(),
                'user_id' => $user->id
            ]);
            $notification = Notification::create($request->all());
            return new NotificationResource($notification);

        } catch (\Exception $e) {
            throw new ApiException($e, 'NS-01');
        }
    }


    public function update(Request $request)
    {
        Notification::where('uuid', $request->notification_id)->touch('read_at');

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
}
