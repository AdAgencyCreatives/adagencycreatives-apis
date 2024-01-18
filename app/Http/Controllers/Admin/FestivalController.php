<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\QueryBuilder\QueryBuilder;

class FestivalController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.festivals.index');

    }

    public function downloadFestivals(Request $request)
    {
        $query = QueryBuilder::for(Festival::class)
            ->allowedFilters([
                'email',
                'category',
            ]);

        $festivals = $query->get();

        $filename = 'festivals_' . date('Y-m-d_H-i-s') . '.xlsx';

        $festivals = $festivals->map(function ($festival) {
            return [
                'First Name' => $festival->first_name,
                'Last Name' => $festival->last_name,
                'Email' => $festival->email,
                'Title' => $festival->title,
                'Category' => $festival->category,
                'Video URL' => getAttachmentBasePath() . $festival->path,
                'created_at' => $festival->created_at->format('Y-m-d'),
            ];
        });

        return (new FastExcel($festivals))->download($filename);

    }

}