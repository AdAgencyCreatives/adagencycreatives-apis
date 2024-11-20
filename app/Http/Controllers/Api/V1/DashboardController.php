<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Application;
use App\Models\Bookmark;
use App\Models\Creative;
use App\Models\Job;
use App\Models\Message;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    //For admin dashboard
    public function index()
    {
        $cacheKey = 'dashboard_stats_cache';
        $cacheDuration = Carbon::now()->addHours(2);
        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            return $cachedData;
        } else {
            /*
             * Users count based on role
             */
            $total_users = User::count();
            $admin_users = User::where('role', 1)->count();
            $advisor_users = User::where('role', 2)->count();
            $agency_users = User::where('role', 3)->count();
            $creative_users = User::where('role', 4)->count();

            /*
             * Users count based on status
             */
            $pending_users = User::where('status', 0)->count();
            $active_users = User::where('status', 1)->count();
            $inactive_users = User::where('status', 2)->count();

            /*
             * Jobs count based on status
             */
            $total_jobs = Job::count();
            $pending_jobs = Job::where('status', 0)->count();
            $approved_jobs = Job::where('status', 1)->count();
            $rejected_jobs = Job::where('status', 2)->count();
            $expired_jobs = Job::where('status', 3)->count();
            $filled_jobs = Job::where('status', 4)->count();

            $remote_jobs = Job::where('is_remote', 1)->count();
            $onsite_jobs = Job::where('is_onsite', 1)->count();
            $featured_jobs = Job::where('is_featured', 1)->count();
            $hybrid_jobs = Job::where('is_hybrid', 1)->count();
            $urgent_jobs = Job::where('is_urgent', 1)->count();

            /*
             * Revenue based on different plans
             */

            $total_amount = Order::sum('amount');
            $basic_amounty = Order::where('plan_id', 1)->sum('amount');
            $standard_amounty = Order::where('plan_id', 2)->sum('amount');
            $premium_amounty = Order::where('plan_id', 3)->sum('amount');

            $response = [

                'total_amount' => '$' . $total_amount,
                'post-a-creative-job-amount' => '$' . $basic_amounty,
                'multiple-creative-jobs-amount' => '$' . $standard_amounty,
                'premium-creative-jobs-amount' => '$' . $premium_amounty,

                'total_users' => $total_users,
                'admin_users' => $admin_users,
                'agency_users' => $agency_users,
                'creative_users' => $creative_users,

                'advisor_users' => $advisor_users,
                'pending_users' => $pending_users,
                'active_users' => $active_users,
                'inactive_users' => $inactive_users,

                'total_jobs' => $total_jobs,
                'pending_jobs' => $pending_jobs,
                'approved_jobs' => $approved_jobs,
                'rejected_jobs' => $rejected_jobs,
                'expired_jobs' => $expired_jobs,
                'filled_jobs' => $filled_jobs,

                'remote_jobs' => $remote_jobs,
                'onsite_jobs' => $onsite_jobs,
                'featured_jobs' => $featured_jobs,
                'hybrid_jobs' => $hybrid_jobs,
                'urgent_jobs' => $urgent_jobs,

                'orders_chart' => $this->chartData(Order::class),
                'users_chart' => $this->chartData(User::class),
                'jobs_chart' => $this->chartData(Job::class),

            ];

            Cache::put($cacheKey, $response, $cacheDuration);

            return response()->json($response);
        }
    }

    public function chartData($ModelName)
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        // Generate an array of dates between the start and end dates
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateRange[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        $data = $ModelName::where('created_at', '>', $startDate)
            ->orderBy('created_at')
            ->get();

        // Create an associative array with dates as keys and initial count as 0
        $createdCount = array_fill_keys($dateRange, 0);

        // Count the records for each date
        $groupedData = $data->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function ($group) {
            return $group->count();
        });

        // Merge the counts into the createdCount array
        foreach ($groupedData as $date => $count) {
            $createdCount[$date] = $count;
        }

        return [
            'labels' => $dateRange,
            'createdData' => $createdCount,
        ];
    }

    //For Agency Dashboard
    public function agency_dashboard_stats()
    {
        $user = get_auth_user();
        $cacheKey = 'agency_dashboard_stats_' . $user->id;

        //$stats = Cache::remember($cacheKey, 60, function () use ($user) {
        $jobs = Job::where('user_id', $user->id)
            ->orWhere('advisor_id', $user->id)
            ->get();

        $jobs_count = $jobs->count();

        $applications = Application::whereIn('job_id', $jobs->pluck('id'))
            ->whereHas('user')->get();
        $applications_count = $applications->count();

        $shortlisted_count = Bookmark::where('user_id', $user->id)->count();

        $agency = Agency::where('user_id', $user->id)->first();
        $profile_views_count = $agency ? $agency->views : 0;

        $stats = [
            'number_of_posts' => $jobs_count,
            'applications' => $applications_count,
            'shortlisted' => $shortlisted_count,
            'review' => $profile_views_count, // This key we will remove later, because we have renamed it to views
            'views' => $profile_views_count,
        ];

        //     return $stats;
        // });

        return response()->json([
            'stats' => $stats,
        ]);
    }

    //For Creative Dashboard
    public function creative_dashboard_stats()
    {
        $user = get_auth_user();
        $cacheKey = 'creative_dashboard_stats_' . $user->id;

        //$stats = Cache::remember($cacheKey, 60, function () use ($user) {
        $jobs = Job::where('status', 1)->pluck('id');
        $applied_jobs = Application::whereIn('job_id', $jobs)->where('user_id', $user->id)->count();

        $unread_messages = Message::where('receiver_id', $user->id)->whereNull('read_at')->whereIn('type', ['job', 'private'])->count();
        $creative = Creative::where('user_id', $user->id)->first();
        $profile_views_count = $creative->views;
        $shortlisted_count = Bookmark::where('user_id', $user->id)->count();

        $stats = [
            'jobs_applied' => $applied_jobs,
            'review' => $unread_messages,
            'views' => $profile_views_count,
            'shortlisted' => $shortlisted_count,
        ];

        //     return $stats;
        // });

        return response()->json([
            'stats' => $stats,
        ]);
    }


    public function findAgencyBookmarkCount($agency_id) //How many people have bookmarked this agency
    {
        return Bookmark::where('bookmarkable_type', 'App\Models\Agency')
            ->where('bookmarkable_id', $agency_id)
            ->count();
    }
}
