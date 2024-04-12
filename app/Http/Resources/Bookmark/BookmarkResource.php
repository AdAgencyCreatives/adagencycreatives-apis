<?php

namespace App\Http\Resources\Bookmark;

use App\Models\Creative;
use App\Models\Agency;
use App\Models\Job;
use App\Http\Resources\Agency\AgencyResource;
use App\Http\Resources\Creative\CreativeResource;
use App\Http\Resources\Job\JobResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this?->uuid,
            'user_id' => $this?->user?->uuid,
            'resource_type' => $this?->bookmarkable_type,
            'resource' => $this?->mapResourcePath(),
            'created_at' => $this?->created_at?->format(config('global.datetime_format')),
            'updated_at' => $this?->updated_at?->format(config('global.datetime_format')),
        ];

        return $data;
    }

    public function mapResourcePath()
    {
        // $model = $this->bookmarkable_type::where('id', $this->bookmarkable_id)->firstOrFail();
        // if ($model->user) {
        //     switch ($this->bookmarkable_type) {
        //         case 'App\Models\Creative':
        //             return new CreativeResource($model);

        //         case 'App\Models\Agency':
        //             return new AgencyResource($model);

        //         case 'App\Models\Job':
        //             return new JobResource($model);

        //         default:
        //             return null;
        //     }
        // }

        switch ($this->bookmarkable_type) {
            case 'App\Models\Creative':
                $model = Creative::where('user_id', $this->bookmarkable_id)->firstOrFail();
                if ($model->user) {
                    return new CreativeResource($model);
                }
            case 'App\Models\Agency':
                $model = Agency::where('user_id', $this->bookmarkable_id)->firstOrFail();
                if ($model->user) {
                    return new AgencyResource($model);
                }
            case 'App\Models\Job':
                $model = Job::where('user_id', $this->bookmarkable_id)->firstOrFail();
                if ($model->user) {
                    return new JobResource($model);
                }
            default:
                return null;
        }
        return null;
    }
}
