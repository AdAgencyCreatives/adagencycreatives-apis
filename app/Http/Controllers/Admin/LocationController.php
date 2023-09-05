<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        return view('pages.locations.state.states');
    }

    public function cities(Location $location)
    {
        return view('pages.locations.city.cities', get_defined_vars());
    }
}
