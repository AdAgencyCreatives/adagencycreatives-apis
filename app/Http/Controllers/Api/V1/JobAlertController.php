<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobAlert\StoreJobAlertRequest;
use App\Http\Resources\JobAlert\JobAlertCollection;
use App\Models\Category;
use App\Models\JobAlert;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class JobAlertController extends Controller
{
    public function index(Request $request)
    {

        $user_uuid = $request?->filter['user_id'];

        $sql = "INSERT INTO job_alerts SELECT NULL, UUID(), (SELECT id FROM users WHERE uuid = '" . $user_uuid . "'), categories.id, 0, NOW(), NOW() FROM categories WHERE categories.id NOT IN (SELECT job_alerts.category_id FROM job_alerts INNER JOIN users ON job_alerts.user_id = users.id WHERE users.uuid = '" . $user_uuid . "');";

        DB::update($sql);

        $query = QueryBuilder::for(JobAlert::class)
            ->allowedFilters(
                [
                    AllowedFilter::scope('user_id'),
                    'status',
                ]
            );

        $alerts = $query->get()->sortBy('category.name');

        if (!empty($alerts) &&  $alerts->count() > 0) {
            return new JobAlertCollection($alerts);
        }

        return ApiResponse::error('Job alert not found', 404);
    }

    public function store(StoreJobAlertRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();

        $category_ids = Category::whereIn('uuid', $request->category_id)->pluck('id')->toArray();

        try {

            foreach ($category_ids ?? [] as $category_id) {

                $sync[$category_id] = ['uuid' => Str::uuid(), 'status' => $request->status];
            }

            $sync = $user->alert_categories()->sync($sync ?? []);

            $alerts = JobAlert::whereUserId($user->id)->whereIn('category_id', $category_ids)->get()->sortBy('category.name');

            return ApiResponse::success(new JobAlertCollection($alerts), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('SA-01 ' . $e->getMessage(), 400);
        }
    }

    public function update(StoreJobAlertRequest $request, $uuid)
    {
        try {
            $alert = JobAlert::where('uuid', $uuid)->first();
            $alert->update(['status' => $request->status]);
            $alerts = JobAlert::whereUserId($alert->user_id)->get()->sortBy('category.name');
            return new JobAlertCollection($alerts);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
