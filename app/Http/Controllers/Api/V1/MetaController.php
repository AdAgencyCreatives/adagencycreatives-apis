<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Article;
use App\Models\Creative;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    /**
     * Get meta data for SEO purposes.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getMetaData(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $slug = $request->get('slug');

        if (!$type || !$slug) {
            return response()->json(['error' => 'Missing required parameters.'], 400);
        }

        $data = null;

        switch ($type) {
            case 'creative':
                $creative = Creative::where('slug', $slug)->first();
                if ($creative) {
                    $data = [
                        'name' => $creative->user->first_name . ' ' . $creative->user->last_name,
                        'category' => $creative->category?->name,
                        'about' => $creative->about,
                    ];
                }
                break;

            case 'agency':
                $agency = Agency::where('slug', $slug)->first();
                if ($agency) {
                    $data = [
                        'name' => $agency->name,
                        'company' => $agency->name,
                        'about' => $agency->about,
                    ];
                }
                break;

            case 'job':
                $job = Job::where('slug', $slug)->first();
                if ($job) {
                    $data = [
                        'title' => $job->title,
                        'about' => $job->description,
                    ];
                }
                break;

            case 'news':
                $news = Article::where('slug', $slug)->first();
                if ($news) {
                    $data = [
                        'title' => $news->title,
                        'sub_title' => $news->sub_title,
                        'article_date' => $news->article_date,
                        'description' => $news->description,
                    ];
                }
                break;

            default:
                return response()->json(['error' => 'Invalid type.'], 400);
        }

        if (!$data) {
            return response()->json(['error' => 'Record not found.'], 404);
        }

        return response()->json($data);
    }
}
