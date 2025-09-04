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
    /**
     * Display a listing of jobs.
     */
    public function index()
    {
        return view('pages.jobs.index');
    }

    /**
     * Show the featured jobs page.
     */
    public function featuredJobs()
    {
        return view('pages.featured_jobs.index');
    }

    /**
     * Show the form for creating a new job.
     */
    public function create()
    {
        return view('pages.jobs.add-job');
    }

    /**
     * Display the specified job details.
     */
    public function details($id)
    {
        $job = Job::with('applications', 'attachment')->where('uuid', $id)->first();
        $agency_logo = get_agency_logo($job, $job->user);
        // dd($job->toArray());
        return view('pages.jobs.detail.detail', compact('job', 'agency_logo'));
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request)
    {
        $category = Category::where('uuid', $request->category_id)->first();
        $state = Location::where('uuid', $request->state)->first();
        $city = Location::where('uuid', $request->city)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => auth()->id(),
            'category_id' => $category->id,
            'address_id' => 5,
            'status' => $request->status,
            'industry_experience' => '' . implode(',', array_slice($request->industry_experience ?? [], 0, 10)) . '',
            'media_experience' => '' . implode(',', array_slice($request->media_experience ?? [], 0, 10)) . '',
            'strengths' => '' . implode(',', array_slice($request->strengths ?? [], 0, 10)) . '',
            'state_id' => $state->id ?? 1,
            'city_id' => $city->id ?? 1,
        ]);

        $labels = $request->labels;

        foreach ($labels as $label) {
            $request->merge([
                $label => 1,
            ]);
        }
        $job = Job::create($request->all());

        if ($request->hasFile('file')) {
            $attachment = storeImage($request, auth()->id(), 'agency_logo');
            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $job->id,
                ]);
            }
        }

        Session::flash('success', 'Job created successfully');

        return redirect()->back();
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, $id)
    {
        $job = Job::where('id', $id)->first();

        $category = Category::where('uuid', $request->category_id)->first();
        $state = Location::where('uuid', $request->state)->first();
        $city = Location::where('uuid', $request->city)->first();

        $request->merge([
            'category_id' => $category->id,
            'address_id' => 5,
            'industry_experience' => '' . implode(',', array_slice($request->industry_experience ?? [], 0, 10)) . '',
            'media_experience' => '' . implode(',', array_slice($request->media_experience ?? [], 0, 10)) . '',
            'strengths' => '' . implode(',', array_slice($request->strengths ?? [], 0, 10)) . '',
            'state_id' => $state->id ?? $job->state_id,
            'city_id' => $city->id ?? $job->city_id,
        ]);

        $this->appendWorkplacePreference($request);


        if ($request?->is_featured && !$job?->is_featured) {
            $job->featured_at = now();
        }

        if ($job?->is_featured && !$request?->is_featured) {
            $job->featured_at = null;
        }

        $job->update($request->all());

        if ($request->hasFile('file')) {
            $attachment = storeImage($request, $job->user_id, 'sub_agency_logo');
            if (isset($attachment) && is_object($attachment)) {
                Attachment::whereId($attachment->id)->update([
                    'resource_id' => $job->id,
                ]);

                $job->attachment_id = $attachment->id;
                $job->save();
            }
        }

        Session::flash('success', 'Job updated successfully');

        return redirect()->back();
    }

    /**
     * Reorders featured jobs on drag and drop.
     */
    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $itemId) {
            Job::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }

    /**
     * Updates the sort order for a single featured job.
     */
    public function updateOrderSingle(Request $request)
    {
        $order = $request->input('sort_order');
        $job_id = $request->input('job_id');

        Job::where('id', $job_id)->update(['sort_order' => $order]);

        return response()->json(['message' => 'Order updated successfully', 'status' => 200]);
    }

    /**
     * Stores an image attachment.
     */
    private function storeImage($request, $resource_type)
    {
        $uuid = Str::uuid();
        $file = $request->file;
        $resource_type = 'agency_logo';

        $extension = $file->getClientOriginalExtension();

        $folder = $resource_type . '/' . $uuid;
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

    /**
     * Appends workplace preferences to the request.
     */
    private function appendWorkplacePreference($request)
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

    /**
     * Updates SEO for the specified job.
     */
    public function update_seo(Request $request, $uuid)
    {
        $job = Job::where('uuid', $uuid)->first();
        $job->update([
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => implode(',', $request->seo_keywords ? $request->seo_keywords : []),
        ]);
        Session::flash('success', 'Job updated successfully');

        return redirect()->back();
    }
}
