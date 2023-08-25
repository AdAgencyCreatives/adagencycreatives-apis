<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $orders = Order::with('user', 'plan')
        ->latest()
        ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new OrderCollection($orders);
    }
}
