<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Attachment;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class AgencyController extends Controller
{
    public function update(Request $request, $uuid)
    {
        $agency = Agency::where('uuid', $uuid)->first();
        $user = User::where('id', $agency->user_id)->first();

        $uuid = Str::uuid();
        if($request->has('file') && is_object($request->file)) {
            $file = $request->file;
            $resource_type = 'agency_logo';

            $extension = $file->getClientOriginalExtension();
            $filename = $uuid.'.'.$extension;
            $file_path = Storage::disk('public')->putFileAs($resource_type, $file, $filename);

            $attachment = Attachment::create([
                'uuid' => $uuid,
                'user_id' => $user->id,
                'resource_type' => $resource_type,
                'path' => $file_path,
                'extension' => $extension,
            ]);

        }


        $request->merge([
                        'industry_specialty' => ''.implode(',', $request->industry_specialty).''
                    ]);
        // dd($request->all());
        $data = $request->only(['name', 'size', 'type_of_work', 'industry_specialty', 'about']);
        foreach ($data as $key => $value) {
            $agency->$key = $value;
        }

        $agency->save();

        if ($request->has('linkedin') && $request->input('linkedin') != null) {
            $this->updateLink($user, 'linkedin', $request->input('linkedin'));
        }

        if ($request->has('website') && $request->input('website') != null) {
            $this->updateLink($user, 'website', $request->input('website'));
        }

        if(isset($attachment) && is_object($attachment)) {
            Attachment::whereId($attachment->id)->update([
                        'resource_id' => $agency->id,
                    ]);
        }

        $user->update([
            'is_visible' => $request->is_visible
        ]);

        Session::flash('success', 'Agency updated successfully');
        return redirect()->back();

    }

    private function updateLink($user, $label, $url)
    {
        $link = Link::where('user_id', $user->id)->where('label', $label)->first();
        if ($link) {
            $link->update(['url' => $url]);
        } else {

            Link::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'label' => $label,
                'url' => $url,
            ]);
        }
    }
}