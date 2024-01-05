<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\PublicationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PublicationResourceController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.publication_resource.index');
    }

    public function create(Request $request)
    {
        return view('pages.publication_resource.create');
    }

    public function store(Request $request)
    {
        $resource = PublicationResource::create($request->all());

        if ($request->has('file') && is_object($request->file)) {

            $attachment = storeImage($request, auth()->id(), 'publication_resource_website_preview');

            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $resource->id,
                ]);

                $resource->preview_link = $attachment->path;
                $resource->save();
            }
        }


        Session::flash('success', 'Resource saved successfully');

        return redirect()->back();


    }

    public function edit(Request $request, PublicationResource $publicationResource)
    {
        return view('pages.publication_resource.edit', compact('publicationResource'));
    }

    public function update(Request $request, PublicationResource $publicationResource)
    {

        if ($request->has('file') && is_object($request->file)) {

            $attachment = storeImage($request, auth()->id(), 'publication_resource_website_preview');

            if (isset($attachment) && is_object($attachment)) {

                if ($publicationResource->preview_link) {
                    $this->deleteImage($publicationResource);
                }

                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $publicationResource->id,
                ]);

                $publicationResource->update([
                    'link' => $request->input('link'),
                    'preview_link' => $attachment->path,
                ]);
            }
        } else {
            $publicationResource->update($request->except('file'));
        }

        Session::flash('success', 'Resource updated successfully');
        return back();
    }

    private function deleteImage($resource)
    {
        Attachment::where("resource_id" , $resource->id)
            ->where('resource_type', 'publication_resource_website_preview')
            ->delete();
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $itemId) {
            PublicationResource::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
}
