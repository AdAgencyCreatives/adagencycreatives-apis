<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAdminUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

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
        $job = Job::with('applications')->where('uuid', $id)->first();

        return view('pages.jobs.detail.detail', compact('job'));
    }
}