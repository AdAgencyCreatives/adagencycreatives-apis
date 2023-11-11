<?php

namespace App\Http\Resources\Review;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReviewCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $totalRating = $this->collection->sum('rating');
        $averageRating = $this->collection->avg('rating') ?? 0;
        $total_reviews_count = $this->collection->count();

        return [
            'data' => $this->collection,
            'meta' => [
                'total_rating' => $totalRating,
                'average_rating' => $averageRating,
                'total_reviews_count' => $total_reviews_count,
            ],
        ];
    }
}