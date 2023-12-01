<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\PackageRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PackageRequestController extends Controller
{
    public function index()
    {
        return view('pages.package_requests.index');
    }

    public function details($id)
    {
        $package_request = PackageRequest::with('plan', 'user', 'category')->where('uuid', $id)->first();

        return view('pages.package_requests.detail', compact('package_request'));
    }

    public function update(Request $request, $uuid)
    {
        try {
            $package_request = PackageRequest::where('uuid', $uuid)->firstOrFail();

            if ($request->input('assigned_to') !== '-1') {
                $package_request->assigned_to = $request->input('assigned_to');
            }

            if ($request->input('plan_id') !== '-1') {
                $package_request->plan_id = $request->input('plan_id');
            }

            $package_request->status = $request->input('status');
            $package_request->save();
            Session::flash('success', 'Job updated successfully');

            return redirect()->back();

        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}