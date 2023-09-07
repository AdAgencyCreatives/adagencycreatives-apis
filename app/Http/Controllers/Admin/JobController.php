<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Job;
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
        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => auth()->id(),
            'category_id' => $category->id,
            'address_id' => 5,
            'status' => $request->status,
            'industry_experience' => ''.implode(',', $request->industry_experience).'',
            'media_experience' => ''.implode(',', $request->media_experience).'',
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
        $job = Job::where('id', $id)->first();

        if ($request->hasFile('file')) {
            $attachment = $this->storeImage($request);
        }
        $category = Category::where('uuid', $request->category_id)->first();
        $request->merge([
            'category_id' => $category->id,
            'address_id' => 5,
            'industry_experience' => ''.implode(',', $request->industry_experience).'',
            'media_experience' => ''.implode(',', $request->media_experience).'',
        ]);

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

    public function storeImage($request)
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
}