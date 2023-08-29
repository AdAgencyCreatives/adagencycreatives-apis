<?php

use App\Models\Industry;

if (! function_exists('getIndustryNames')) {
    function getIndustryNames($commaSeparatedIds)
    {
        $ids = explode(',', $commaSeparatedIds);
        $industries = Industry::whereIn('uuid', $ids)->pluck('name')->toArray();

        return $industries;
    }
}