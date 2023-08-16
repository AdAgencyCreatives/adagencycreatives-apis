<?php

use App\Models\Industry;

if (! function_exists('getIndustryNames')) {
    function getIndustryNames($commaSeparatedIds)
    {
        $ids = explode(',', $commaSeparatedIds);
        $industries = Industry::whereIn('id', $ids)->pluck('name')->toArray();

        return $industries;
    }
}
