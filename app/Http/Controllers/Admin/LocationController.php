<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocationController extends Controller
{
    public function index()
    {
        return view('pages.locations.state.states');
    }

    public function create()
    {
        return view('pages.locations.state.add');
    }

    public function edit(Location $location)
    {
        $is_state = is_null($location->parent_id);

        $link = null;
        $attachmentBasePath = getAttachmentBasePath();
        if($location->preview_link){
            $link =  $attachmentBasePath . $location->preview_link;
        }

        return view('pages.locations.state.edit', compact('location', 'is_state', 'link'));
    }
    
    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);
        
        $location->name = $request->name;
    
        if ($request->has('file') && is_object($request->file)) {

            $attachment = storeImage($request, auth()->id(), 'location_preview');
            
            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $location->id,
                ]);

                $location->preview_link = $attachment->path;
                $location->save();
            }
        }

        $location->is_featured = $request->has('featured') ? 1 : 0;
    
        $location->save();
        Session::flash('success', 'Location updated successfully');

        return back();
    }

    public function city_create()
    {
        return view('pages.locations.city.add');
    }

    public function cities(Location $location)
    {
        return view('pages.locations.city.cities', get_defined_vars());
    }
}
