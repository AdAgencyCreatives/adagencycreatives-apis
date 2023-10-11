<?php

namespace App\Http\Resources\Creative;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class CreativeSpotlightCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            $user = $item->user;
            $title = sprintf('%s, %s', $user->creative?->category?->name, $user->first_name.' '.$user->last_name);

            return [
                'id' => $item->uuid,
                'title' => $title,
                'slug' => Str::slug($title, '-'),
                'url' => getAttachmentBasePath().$item->path,
                'seo' => $this->generateSeoTitle($title, $item->created_at),
                'created_at' => $item->created_at->format('F j, Y'),
            ];
        });
    }

    private function generateSeoTitle($title, $created_at)
    {
        $site_name = settings('site_name');
        $separator = settings('separator');

        $seo_title_format = settings('creative_spotlight_title');

        return replacePlaceholders($seo_title_format, [
            '%post_name%' => $title,
            '%post_date%' => $created_at->format('Y-m-d'),
            '%site_name%' => $site_name,
            '%separator%' => $separator,
        ]);
    }
}
