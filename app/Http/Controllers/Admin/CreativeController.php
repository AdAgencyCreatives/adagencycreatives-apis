<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creative;
use App\Models\Attachment;
use App\Models\Link;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CreativeController extends Controller
{
    public function update(Request $request, $uuid)
    {
        $creative = Creative::where('uuid', $uuid)->first();
        $user = User::where('id', $creative->user_id)->first();

        $uuid = Str::uuid();
        $data = $request->only(['years_of_experience', 'type_of_work']);
        foreach ($data as $key => $value) {
            $creative->$key = $value;
        }

        $creative->save();

        $user->update([
            'is_visible' => $request->is_visible
        ]);

        if ($request->input('country_code') != null && $request->input('phone') != null) {
            $this->updatePhone($user, $request->input('country_code'), $request->input('phone'));
        }

        if ($request->has('linkedin') && $request->input('linkedin') != null) {
            $this->updateLink($user, 'linkedin', $request->input('linkedin'));
        }


        Session::flash('success', 'Creative updated successfully');
        return redirect()->back();

    }

    public function update_qualification(Request $request, $uuid)
    {
        $creative = Creative::where('uuid', $uuid)->first();
        $user = User::where('id', $creative->user_id)->first();
        $uuid = Str::uuid();

    

        if ($request->input('portfolio') != null) {
            $this->updateLink($user, 'portfolio', $request->input('portfolio'));
        }

        if ($request->input('linkedin') != null) {
            $this->updateLink($user, 'linkedin', $request->input('linkedin'));
        }

        $creative->update([
            'industry_experience' => ''.implode(',', $request->industry_experience).'',
            'media_experience' => ''.implode(',', $request->media_experience).'',
        ]);



        Session::flash('success', 'Creative updated successfully');
        return redirect()->back();

    }   
    
    public function update_experience(Request $request, $uuid)
    {
        // dd($request->all());
        $creative = Creative::where('uuid', $uuid)->first();
        $user = User::where('id', $creative->user_id)->first();
        $uuid = Str::uuid();

    

        if ($request->input('portfolio') != null) {
            $this->updateLink($user, 'portfolio', $request->input('portfolio'));
        }

        if ($request->input('linkedin') != null) {
            $this->updateLink($user, 'linkedin', $request->input('linkedin'));
        }

        $creative->update([
            'industry_experience' => ''.implode(',', $request->industry_experience).'',
            'media_experience' => ''.implode(',', $request->media_experience).'',
        ]);



        Session::flash('success', 'Creative updated successfully');
        return redirect()->back();

    }

    private function updatePhone($user, $country_code, $phone_number)
    {
        $phone = Phone::where('user_id', $user->id)->where('label', 'personal')->first();
        if ($phone) {
            $phone->update(['country_code' => $country_code, 'phone_number' => $phone_number]);
        } else {

            Phone::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'label' => 'personal',
                'country_code' => $country_code,
                'phone_number' => $phone_number,
            ]);
        }
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