<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreResumeRequest;
use App\Http\Requests\Resume\UpdateResumeRequest;
use App\Http\Resources\Creative\CreativeResource;
use App\Http\Resources\Resume\ResumeCollection;
use App\Http\Resources\Resume\ResumeResource;
use App\Models\Creative;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(Resume::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
            ]);

        $resumes = $query->paginate(config('global.request.pagination_limit'));

        return new ResumeCollection($resumes);
    }

    public function store(StoreResumeRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
        ]);
        try {
            $resume = Resume::create($request->all());

            return ApiResponse::success(new ResumeResource($resume), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('RS-01 '.$e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $resume = Resume::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new ResumeResource($resume);
    }

    public function update(UpdateResumeRequest $request, $uuid)
    {
        try {
            $resume = Resume::where('uuid', $uuid)->first();
            $resume->update($request->only('years_of_experience', 'about', 'industry_specialty', 'media_experience'));

            return new ResumeResource($resume);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $resume = Resume::where('uuid', $uuid)->firstOrFail();
            $resume->delete();

            return ApiResponse::success($resume, 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function download_resume(Request $request)
    {

        $creative_user = User::where('uuid', $request->u1)->firstOrFail();
        $auth_user = User::where('uuid', $request->u2)->firstOrFail();

        $creative = Creative::with([
            'user.profile_picture',
            'user.addresses.state',
            'user.addresses.city',
            'user.personal_phone',
            'category',
            'user',
    ])
        ->where('user_id', $creative_user->id)
        ->first();

        $educations = $creative_user->educations;
        $experiences = $creative_user->experiences;
        $portfolio_items = $creative_user->portfolio_items;
        if($creative_user->portfolio_website_preview){
            $portfolio_website_preview_img = getAttachmentBasePath() . $creative_user->portfolio_website_preview->path;
        }
        else{
            $portfolio_website_preview_img = null;
        }
        $data = (new CreativeResource($creative))->toArray([]);

        //If user role is creative, then hide phone number from resume
        if($auth_user->role == 'creative'){
            if($auth_user->id != $creative_user->id)
            {
                $data['phone_number'] = '';
            }

        }

        $user = $creative_user;
        $html = view('resume', compact('data', 'user', 'educations', 'experiences', 'portfolio_items', 'portfolio_website_preview_img')); // Render the HTML view

        return $html;
    }

    public function download_resume2($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $creative = Creative::with([
            'user.profile_picture',
            'user.addresses.state',
            'user.addresses.city',
            'user.personal_phone',
            'category',
            'user',
        ])
            ->where('user_id', $user->id)
            ->first();

        $educations = $user->educations;
        $experiences = $user->experiences;
        $portfolio_items = $user->portfolio_items;
        $data = (new CreativeResource($creative))->toArray([]);

        $html = view('resume', compact('data', 'user', 'educations', 'experiences', 'portfolio_items')); // Render the HTML view

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');

        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->render();

        $fileName = sprintf('%s-%s', Str::slug($data['name']), Str::slug($data['title']));
        $dompdf->stream($fileName, ['Attachment' => 1]);

        return 'File downloaded.';

    }
}