<?php

use App\Models\Attachment;
use App\Models\Industry;
use App\Models\Media;
use App\Models\Strength;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('getIndustryNames')) {
    function getIndustryNames($commaSeparatedIds)
    {
        $ids = explode(',', $commaSeparatedIds);
        $industries = Industry::whereIn('uuid', $ids)->pluck('name')->toArray();

        return $industries;
    }
}

if (! function_exists('getMediaNames')) {
    function getMediaNames($commaSeparatedIds)
    {
        $ids = explode(',', $commaSeparatedIds);
        $medias = Media::whereIn('uuid', $ids)->pluck('name')->toArray();

        return $medias;
    }
}

if (! function_exists('getCharacterStrengthNames')) {
    function getCharacterStrengthNames($commaSeparatedIds)
    {
        $ids = explode(',', $commaSeparatedIds);
        $strengths = Strength::whereIn('uuid', $ids)->pluck('name')->toArray();

        return $strengths;
    }
}

if (! function_exists('getAttachmentBasePath')) {
    function getAttachmentBasePath()
    {
        return 'https://ad-agency-creatives.s3.amazonaws.com/';
    }
}

/**
 * This method is called from Admin Controllers
 */
if (! function_exists('storeImage')) {
    function storeImage($request, $user_id, $resource_type)
    {
        $uuid = Str::uuid();
        $file = $request->file;

        $extension = $file->getClientOriginalExtension();
        $folder = $resource_type.'/'.$uuid;
        $filePath = Storage::disk('s3')->put($folder, $file);

        $attachment = Attachment::create([
            'uuid' => $uuid,
            'user_id' => $user_id,
            'resource_type' => $resource_type,
            'path' => $filePath,
            'extension' => $extension,
        ]);

        return $attachment;
    }
}
