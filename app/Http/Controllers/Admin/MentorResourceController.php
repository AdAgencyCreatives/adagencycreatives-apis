<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMentorVisuals;
use App\Models\Attachment;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MentorResourceController extends Controller
{
    public function index(Request $request)
    {
        $resources = Resource::all();

        return view('pages.resource.index', compact('resources'));
    }

    public function create(Request $request)
    {
        return view('pages.resource.create');
    }

    public function store(Request $request)
    {
        $resource = Resource::create($request->all());

        if ($request->has('file') && is_object($request->file)) {
            $attachment = storeImage($request, auth()->id(), 'mentor_resource_website_preview');

            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $resource->id,
                ]);

                $resource->preview_link = $attachment->path;
                $resource->save();
            }
        } else {
            $data = [
                    'id' => $resource->id,
                    'url' => $resource->link,
                    'resource_type' => 'mentor_resource',
                ];
            ProcessMentorVisuals::dispatch($data);
        }

        Session::flash('success', 'Resource saved successfully');
        return redirect()->back();
    }

    public function edit(Request $request, Resource $resource)
    {
        return view('pages.resource.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        if ($request->has('file') && is_object($request->file)) {

            $attachment = storeImage($request, auth()->id(), 'mentor_resource_website_preview');

            if (isset($attachment) && is_object($attachment)) {

                if ($resource->preview_link) {
                    $this->deleteImage($resource);
                }

                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $resource->id,
                ]);

                $resource->update([
                    'topic_id' => $request->input('topic_id'),
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'link' => $request->input('link'),
                    'preview_link' => $attachment->path,
                ]);
            }
        } else {
            $resource->update($request->except('file'));
        }

        Session::flash('success', 'Resource updated successfully');
        return back();
    }

    private function deleteImage($resource)
    {
        Attachment::where("resource_id" , $resource->id)
            ->where('resource_type', 'mentor_resource_website_preview')
            ->delete();
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $itemId) {
            Resource::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
}