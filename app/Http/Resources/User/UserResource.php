<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'type' => 'users',
            'id' => $this->uuid,
            'attributes' => [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'username' => $this->username,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,
                'created_at' => $this->created_at->format(config('ad-agency-settings.datetime_format')),
            ],
            'relationships' => [
                'phones' => [
                    'links' => [
                        'related' => '',
                    ],
                ],
                'addresses' => [],
                'links' => [],
                'jobs' => [],
            ],
            'links' => [
                'self' => route('users.show', $this->uuid),
            ],

        ];

        if ($this->role == 'agency') {
            if ($this->agency) {
                $data['relationships']['agencies'] = [
                    'links' => [
                        'related' => route('agencies.show', $this->agency->uuid),
                    ],
                ];
            }
        } elseif ($this->role == 'creative') {
            if ($this->creative) {
                $data['relationships']['creatives'] = [
                    'links' => [
                        'related' => route('creatives.show', $this->creative->uuid),
                    ],
                ];
            }
        }

        return $data;
    }
}
