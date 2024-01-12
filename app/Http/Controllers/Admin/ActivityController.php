<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Job;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ActivityController extends Controller
{
    public function index()
    {
        return view('pages.activity_log.index');
    }

    public function details($id)
    {
        $activity = Activity::find($id);
        $activityDetail = json_encode($activity->body, JSON_PRETTY_PRINT);

        return view('pages.activity_log.detail.detail', compact('activityDetail'));
    }

    public function update(Request $request, $id)
    {
        $job = Job::where('id', $id)->first();

        $category = Category::where('uuid', $request->category_id)->first();
        $state = Location::where('uuid', $request->state)->first();
        $city = Location::where('uuid', $request->city)->first();

        $request->merge([
            'category_id' => $category->id,
            'address_id' => 5,
            'industry_experience' => ''.implode(',', array_slice($request->industry_experience ?? [], 0, 10)).'',
            'media_experience' => ''.implode(',', array_slice($request->media_experience ?? [], 0, 10)).'',
            'strengths' => ''.implode(',', array_slice($request->strengths ?? [], 0, 10)).'',
            'state_id' => $state->id ?? $job->state_id,
            'city_id' => $city->id ?? $job->city_id,
        ]);

        $this->appendWorkplacePreference($request);
        $job->update($request->all());

        if ($request->hasFile('file')) {
            $attachment = storeImage($request, $job->user_id, 'agency_logo');
            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $job->id,
                ]);
            }
        }

        Session::flash('success', 'Job updated successfully');

        return redirect()->back();
    }
}