<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Job;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index()
    {
        return view('pages.jobs.index');
    }

    public function create()
    {
        return view('pages.jobs.add-job');
    }

    public function details($id)
    {
        $job = Job::with('applications', 'attachment')->where('uuid', $id)->first();

        // dd($job->toArray());
        return view('pages.jobs.detail.detail', compact('job'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if ($request->hasFile('file')) {
            $attachment = $this->storeImage($request);
        }
        $category = Category::where('uuid', $request->category_id)->first();
        $state = Location::where('uuid', $request->state)->first();
        $city = Location::where('uuid', $request->city)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => auth()->id(),
            'category_id' => $category->id,
            'address_id' => 5,
            'status' => $request->status,
            'industry_experience' => ''.implode(',', $request->industry_experience).'',
            'media_experience' => ''.implode(',', $request->media_experience).'',
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $labels = $request->labels;

        foreach ($labels as $label) {
            $request->merge([
                $label => 1,
            ]);

        }
        $job = Job::create($request->all());

        if (isset($attachment) && is_object($attachment)) {
            Attachment::whereId($attachment->id)->update([
                'resource_id' => $job->id,
            ]);
        }

        Session::flash('success', 'Job created successfully');

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $job = Job::where('id', $id)->first();

        if ($request->hasFile('file')) {
            $attachment = storeImage($request, $job->user_id, 'agency_logo');
            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $job->id,
                ]);
            }
        }
        $category = Category::where('uuid', $request->category_id)->first();
        $request->merge([
            'category_id' => $category->id,
            'address_id' => 5,
            'industry_experience' => ''.implode(',', $request->industry_experience).'',
            'media_experience' => ''.implode(',', $request->media_experience).'',
        ]);

        $this->appendWorkplacePreference($request);
        $job->update($request->all());

        if (isset($attachment) && is_object($attachment)) {
            $job->attachment?->delete();
            Attachment::whereId($attachment->id)->update([
                'resource_id' => $job->id,
            ]);
        }

        Session::flash('success', 'Job updated successfully');

        return redirect()->back();
    }

    public function storeImage($request, $resource_type)
    {
        $uuid = Str::uuid();
        $file = $request->file;
        $resource_type = 'agency_logo';

        $extension = $file->getClientOriginalExtension();

        $folder = $resource_type.'/'.$uuid;
        $filePath = Storage::disk('s3')->put($folder, $file);

        $attachment = Attachment::create([
            'uuid' => $uuid,
            'user_id' => auth()->id(),
            'resource_type' => $resource_type,
            'path' => $filePath,
            'extension' => $extension,
        ]);

        return $attachment;
    }

    public function appendWorkplacePreference($request)
    {
        $defaultWorkplacePreferences = [
            'is_hybrid' => 0,
            'is_featured' => 0,
            'is_remote' => 0,
            'is_onsite' => 0,
            'is_urgent' => 0,
        ];

        $workplacePreferences = $request->input('workplace_experience', []);

        foreach ($workplacePreferences as $value) {
            $defaultWorkplacePreferences[$value] = 1;
        }

        return $request->merge($defaultWorkplacePreferences);
    }
}
