<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'users',
            'id' => $this->uuid,
            'attributes' => [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'username' => $this->username,
                'email' => $this->email,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            ],
            'relationships' => [
                'phones' => [
                    'links' => [
                        'related' => 'phone url will come here',
                    ],
                ],
            ],
            'links' => [
                'self' => route('users.show', $this->uuid),
            ],

        ];
    }
}
