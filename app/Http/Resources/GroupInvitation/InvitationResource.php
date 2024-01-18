<?php

namespace App\Http\Resources\GroupInvitation;

use App\Http\Resources\Creative\ShortCreativeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    public function toArray($request)
    {
        $inviter = $this->inviter?->creative;
        $invitee = $this->invitee?->creative;

        return [
            'id' => $this->uuid,
            'invited_by' => new ShortCreativeResource($inviter),
            'invited_to' => new ShortCreativeResource($invitee),
            'group_id' => $this->group->uuid,
            'status' => $this->status,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
