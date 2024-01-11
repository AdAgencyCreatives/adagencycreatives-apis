<?php

namespace App\Http\Resources\Schedule;

use App\Http\Resources\Post\PostResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'sender' =>  new UserResource($this->sender),
            'recipient' => new UserResource($this->recipient),
            'post' =>  new PostResource($this->post),
            'notification_text' => $this->notification_text,
            'status' => $this->status,
            'type' => $this->type,
            'scheduled_at' => $this->scheduled_at->format(config('global.datetime_format')),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
