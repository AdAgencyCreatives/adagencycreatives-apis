<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\FeaturedLocation;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FeaturedLocationController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.featured_location.index');
    }

    public function create(Request $request)
    {
        return view('pages.featured_location.create');
    }

    public function store(Request $request)
    {
        $resource = FeaturedLocation::create($request->all());

        if ($request->has('file') && is_object($request->file)) {

            $attachment = storeImage($request, auth()->id(), 'featured_location_preview');

            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $resource->id,
                ]);

                $resource->preview_link = $attachment->path;
                $resource->save();
            }
        }


        Session::flash('success', 'City saved successfully');

        return redirect()->back();


    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $itemId) {
            Location::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
}