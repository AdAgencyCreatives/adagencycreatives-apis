<?php

namespace App\Http\Resources\Resume;

use App\Http\Resources\Education\EducationCollection;
use App\Http\Resources\Experience\ExperienceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'resumes',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'years_of_experience' => $this->years_of_experience,
            'about' => $this->about,
            'industry_specialty' => getIndustryNames($this->industry_specialty),
            'media_experience' => getIndustryNames($this->media_experience),
            'educations' => new EducationCollection($this->education),
            'experiences' => new ExperienceCollection($this->experience),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
