<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Message;
use App\Models\PackageRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            $agency = Agency::where('user_id', $package_request->user_id)->firstOrFail();

            if ($request->input('assigned_to') !== '-1') {
                $advisor_id = $request->input('assigned_to');
                $advisor = User::find($advisor_id);
                $package_request->assigned_to = $advisor_id;

                if ($package_request->status == "pending" && $request->input('status') == "approved") {
                    $this->update_package($advisor);
                }

                $agency_url = sprintf('%s/agency/%s', env('FRONTEND_URL'), $agency->slug);
                $msg_data = [
                    'uuid' => Str::uuid(),
                    'sender_id' => $agency->user_id,
                    'receiver_id' => $advisor_id ?? null,
                    'message' => sprintf("<a style='text-decoration:underline; href='%s'>%s</a> has been assigned to %s", $agency_url, $agency->name, $advisor->full_name),
                    'type' => "job",
                ];
                Message::create($msg_data);
            }

            if ($request->input('plan_id') !== '-1') {
                $package_request->plan_id = $request->input('plan_id');
            }

            $package_request->status = $request->input('status');
            if ($request->input('assigned_to') == '-1') {
                $package_request->assigned_to = null;
            }

            $package_request->save();

            Session::flash('success', 'Package request updated successfully');

            return redirect()->back();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    private function update_package($user)
    {

        $subscription = Subscription::where('user_id', $user->id)->latest()->first(); // Retrieve the latest subscription
        $plan = Plan::where('slug', '=', 'premium-hire-an-advisor')->first();

        if ($subscription) {
            $subscription->update([
                'quota_left' => $subscription->quota_left + 1,
            ]);
            $subscription->refresh();
        } else {

            $totalQuota = $plan->quota;
            $endDate = now()->addDays($plan->days);

            $user->subscriptions()->create([
                'user_id' => $user->id,
                'name' => $plan->slug,
                'price' => $plan->price,
                'quantity' => $totalQuota,
                'quota_left' => $totalQuota,
                'ends_at' => $endDate,
            ]);
        }
    }
}
