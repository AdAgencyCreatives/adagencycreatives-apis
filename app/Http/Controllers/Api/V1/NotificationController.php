<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\UpdateNotificationRequest;
use App\Http\Resources\Notification\NotificationCollection;
use App\Models\Notification;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = QueryBuilder::for(Notification::class)
            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        if ($request->has('status')) {
            if ($request->status == 0) {
                $query->whereNull('read_at');
            } elseif ($request->status == 1) {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query
            ->where('user_id', $user->id)
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new NotificationCollection($notifications);
    }

    public function update(UpdateNotificationRequest $request)
    {
        Notification::where('uuid', $request->notification_id)->touch('read_at');

        return response()->json([
            'message' => 'Notification updated successfully',
        ]);
    }
}
