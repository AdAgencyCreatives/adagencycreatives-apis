<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreativeSpotlight;
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
        $this->storeVideo($request, 'accepted');

        Session::flash('success', 'Creative updated successfully');

        return redirect()->back();
    }

    public function edit(Request $request, $uuid)
    {
        $spotlight = CreativeSpotlight::where('uuid', $uuid)->firstOrFail();

        return view('pages.creative_spotlights.edit', compact('spotlight'));
    }

    public function update(Request $request, $id)
    {
        $spotlight = CreativeSpotlight::find($id);

        if ($request->has('file')) {
            $spotlight->delete();
            $this->storeVideo($request, 'accepted');
        } else {
            $spotlight->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'published_at' => $request->published_at,
            ]);
        }

        Session::flash('success', 'Creative updated successfully');

        return redirect()->route('creative_spotlights.index');
    }

    public function storeVideo($request, $status)
    {
        $uuid = Str::uuid();
        $file = $request->file;

        $folder = 'creative_spotlight/' . $uuid;
        $filePath = Storage::disk('s3')->put($folder, $file);

        $filename = $file->getClientOriginalName();

        $attachment = CreativeSpotlight::create([
            'uuid' => $uuid,
            'title' => $request->title,
            'path' => $filePath,
            'name' => $filename,
            'slug' => $request->slug,
            'status' => $status,
            'published_at' => $request->published_at,
        ]);

        return $attachment;
    }
}