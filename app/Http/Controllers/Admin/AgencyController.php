<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Agency;
use App\Models\Attachment;
use App\Models\Link;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AgencyController extends Controller
{
    public function update(Request $request, $uuid)
    {

        $agency = Agency::where('uuid', $uuid)->first();
        $user = User::where('id', $agency->user_id)->first();

        $uuid = Str::uuid();
        if ($request->has('file') && is_object($request->file)) {
            //Delete Previous logo
            if ($user->attachments->where('resource_type', 'agency_logo')->count()) {
                Attachment::where('user_id', $user->id)->where('resource_type', 'agency_logo')->delete();
            }
            $attachment = storeImage($request, $user->id, 'agency_logo');

            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $agency->id,
                ]);
            }
        }

        $request->merge([
            'industry_experience' => '' . implode(',', array_slice($request->industry_experience ?? [], 0, 10)) . '',
            'media_experience' => '' . implode(',', array_slice($request->media_experience ?? [], 0, 10)) . '',
        ]);

        $this->appendWorkplacePreference($request);

        $data = $request->only(['name', 'size', 'industry_experience', 'media_experience', 'about', 'is_featured', 'is_urgent', 'is_remote', 'is_hybrid', 'is_onsite']);

        if ($request?->is_featured && !$agency?->is_featured) {
            $agency->featured_at = now();
        }

        if ($agency?->is_featured && !$request?->is_featured) {
            $agency->featured_at = null;
        }

        foreach ($data as $key => $value) {
            $agency->$key = $value;
        }

        $agency->save();

        if ($request->has('linkedin') && $request->input('linkedin') != null) {
            $this->updateLink($user, 'linkedin', $request->input('linkedin'));
        } else {
            $this->removeLink($user, 'linkedin');
        }

        if ($request->has('website') && $request->input('website') != null) {
            $this->updateLink($user, 'website', $request->input('website'));
        } else {
            $this->removeLink($user, 'website');
        }

        if (isset($attachment) && is_object($attachment)) {
            Attachment::whereId($attachment->id)->update([
                'resource_id' => $agency->id,
            ]);
        }

        $user->update([
            'is_visible' => $request->is_visible,
        ]);

        $this->updateLocation($request, $user);

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

    private function removeLink($user, $label)
    {
        Link::where('user_id', $user->id)->where('label', $label)->delete();
    }

    public function appendWorkplacePreference($request)
    {
        $defaultWorkplacePreferences = [
            'is_hybrid' => 0,
            'is_remote' => 0,
            'is_onsite' => 0,
        ];

        $workplacePreferences = $request->input('workplace_experience', []);

        foreach ($workplacePreferences as $value) {
            $defaultWorkplacePreferences[$value] = 1;
        }

        return $request->merge($defaultWorkplacePreferences);
    }

    private function updateLocation($request, $user)
    {
        $state = Location::where('uuid', $request->state)->first();
        $city = Location::where('uuid', $request->city)->first();
        if ($state && $city) {
            $address = $user->addresses->first();
            if (!$address) {
                $address = new Address();
                $address->uuid = Str::uuid();
                $address->user_id = $user->id;
                $address->label = 'business';
                $address->country_id = 1;
            }
            $address->state_id = $state->id;
            $address->city_id = $city->id;
            $address->save();
        }
    }

    public function update_seo(Request $request, $uuid)
    {
        $agency = Agency::where('uuid', $uuid)->first();
        $agency->update([
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => implode(',', $request->seo_keywords ? $request->seo_keywords : []),
        ]);
        Session::flash('success', 'Agency updated successfully');

        return redirect()->back();
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $itemId) {
            Agency::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }

        // Cache::forget('homepage_agencies');

        return response()->json(['message' => 'Order updated successfully']);
    }
    
    public function updateOrderSingle(Request $request)
    {
        $order = $request->input('sort_order');
        $agency_id = $request->input('agency_id');

        Agency::where('id', $agency_id)->update(['sort_order' => $order]);
        
        // Cache::forget('homepage_agencies');

        return response()->json(['message' => 'Order updated successfully', 'status' => 200]);
    }
}
