<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        //Uncomment this code if you want to hide admin from the response
        // if ($this->role == 'admin') {
        //     return [];
        // }

        if ($this->role == 'creative') {
            $image = $this->profile_picture ? getAttachmentBasePath().$this->profile_picture->path : null;
        } elseif ($this->role == 'agency' || $this->role == 'advisor') {
            $image = $this->agency_logo ? getAttachmentBasePath().$this->agency_logo->path : null;
        }

        $data = [
            'type' => 'users',
            'uuid' => $this->uuid,
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'is_visible' => $this->is_visible,
            'image' => $image ?? null,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),

            'relationships' => [
                'phones' => [
                    'links' => [
                        'related' => route('phone-numbers.index').'?filter[user_id]='.$this->uuid,
                    ],
                ],
                'addresses' => [
                    'links' => [
                        'related' => route('addresses.index').'?filter[user_id]='.$this->uuid,
                    ],
                ],
                'attachments' => [
                    'links' => [
                        'related' => route('attachments.index').'?filter[user_id]='.$this->uuid,
                    ],
                ],
                'links' => [
                    'links' => [
                        'related' => route('links.index').'?filter[user_id]='.$this->uuid,
                    ],
                ],
                'bookmarks' => [
                    'links' => [
                        'related' => route('bookmarks.index').'?filter[user_id]='.$this->uuid,
                    ],
                ],

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

                $data['relationships']['jobs'] = [
                    'links' => [
                        'related' => route('jobs.index').'?filter[user_id]='.$this->uuid,
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

                $data['relationships']['applications'] = [
                    'links' => [
                        'related' => route('applications.index').'?filter[user_id]='.$this->uuid,
                    ],
                ];

                $data['relationships']['resumes'] = [
                    'links' => [
                        'related' => route('resumes.index').'?filter[user_id]='.$this->uuid,
                    ],
                ];
            }
        }

        return $data;
    }
}
