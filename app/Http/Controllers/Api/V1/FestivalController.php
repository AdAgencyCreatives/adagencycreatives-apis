<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Http\Resources\Festival\FestivalCollection;
use App\Http\Resources\Festival\FestivalResource;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class FestivalController extends Controller
{

    public function index(Request $request)
    {
        $query = QueryBuilder::for(Festival::class)
            ->allowedFilters([
                'first_name',
                'last_name',
                'email',
                'title',
                'category',
            ])
             ->allowedSorts('created_at', 'first_name', 'last_name', 'email', 'title', 'category');

        $festivals = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new FestivalCollection($festivals);
    }




    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'email' => 'required|email',
            'title' => 'required|string',
            'file' => 'required',
            'category' => 'required|string',
        ]);

        $festival = Festival::create($request->all());

        $attachment = storeImage($request, 0, 'film_festival');

        if (isset($attachment) && is_object($attachment)) {
            Attachment::whereId($attachment->id)->update([
                'resource_id' => $festival->id,
            ]);

            $festival->update([
                'path' => $attachment->path,
            ]);
        }

         return new FestivalResource($festival);
    }


    public function destroy(Festival $festival)
    {
        //
    }
}