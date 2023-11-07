<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CreativeSpotlight\CreativeSpotlightResource;
use App\Models\CreativeSpotlight;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreativeSpotlightController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.creative_spotlights.index');
    }

    public function create(Request $request)
    {
        return view('pages.creative_spotlights.add');
    }

    public function store(Request $request)
    {
        // if authro is missing from request, send back error, not the json error, but the error from the form
        if (!$request->author) {
            return redirect()->back()->withErrors(['author' => 'Author is required']);
        }

        $user = User::find($request->author);

        $this->storeVideo($request, $user, 'accepted');

        Session::flash('success', 'Creative updated successfully');

        return redirect()->back();
    }

    public function edit(Request $request, $uuid)
    {
        $spotlight = CreativeSpotlight::where('uuid', $uuid)->firstOrFail();
        return view('pages.creative_spotlights.edit', compact('spotlight'));
    }


    public function update(Request $request, $uuid)
    {
        try {
            $attachment = CreativeSpotlight::where('uuid', $uuid)->firstOrFail();
            $attachment->update($request->only('status'));

            return new CreativeSpotlightResource($attachment);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function storeVideo($request, $user, $status)
    {
        $uuid = Str::uuid();
        $file = $request->file;

        $folder = 'creative_spotlight/' . $uuid;
        $filePath = Storage::disk('s3')->put($folder, $file);

        $filename = $file->getClientOriginalName();

        $attachment = CreativeSpotlight::create([
            'uuid' => $uuid,
            'user_id' => $user->id,
            'user_name' => $user->full_name,
            'title' => $request->title,
            'path' => $filePath,
            'name' => $filename,
            'slug' => $request->slug,
            'status' => $status,
        ]);

        return $attachment;
    }
}