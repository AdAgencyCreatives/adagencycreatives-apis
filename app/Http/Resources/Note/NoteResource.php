<?php

namespace App\Http\Resources\Note;

use App\Http\Resources\Agency\AgencyResource;
use App\Http\Resources\Application\ApplicationResource;
use App\Http\Resources\Creative\CreativeResource;
use App\Http\Resources\Job\JobResource;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'body' => $this->body,
            'resource_type' => $this->notable_type,
            'resource' => $this->mapResourcePath(),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];

        return $data;
    }

    public function mapResourcePath()
    {
        $model = $this->notable_type::where('id', $this->notable_id)->firstOrFail();
        switch ($this->notable_type) {
            case 'App\Models\Creative':
                return new CreativeResource($model);

            case 'App\Models\Agency':
                return new AgencyResource($model);

            case 'App\Models\Job':
                return new JobResource($model);

            case 'App\Models\Application':
                return new ApplicationResource($model);

            default:
                return null;
        }
    }
}
