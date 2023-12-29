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
        }
        else{
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

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $itemId) {
            Resource::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
}
