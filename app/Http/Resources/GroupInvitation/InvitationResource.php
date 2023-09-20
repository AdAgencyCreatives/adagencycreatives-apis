<?php

namespace App\Http\Resources\GroupInvitation;

use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    public function toArray($request)
    {
        $inviter = $this->inviter;
        $invitee = $this->invitee;

        return [
            'id' => $this->uuid,
            'invited_by' => [
                'id' => $inviter->uuid,
                'name' => $inviter->first_name.' '.$inviter->last_name,
            ],
            'invited_to' => [
                'id' => $invitee->uuid,
                'name' => $invitee->first_name.' '.$invitee->last_name,
            ],
            'group_id' => $this->group->uuid,
            'status' => $this->status,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
