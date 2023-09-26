<?php

namespace App\Services;

class UserService
{
    public function appendWorkplacePreference($request)
    {
        $defaultWorkplacePreferences = [
            'is_hybrid' => 0,
            'is_remote' => 0,
            'is_onsite' => 0,
        ];

        $workplacePreferences = $request->input('workplace_experience', []);

        foreach ($workplacePreferences as $value) {
            $defaultWorkplacePreferences[$value] = 1;
        }

        return $request->merge($defaultWorkplacePreferences);
    }

    public function appendFeaturedAndUrgent($request)
    {
        $defaultWorkplacePreferences = [
            'is_featured' => 0,
            'is_urgent' => 0,
        ];

        $workplacePreferences = $request->input('workplace_experience', []);

        foreach ($workplacePreferences as $value) {
            $defaultWorkplacePreferences[$value] = 1;
        }

        return $request->merge($defaultWorkplacePreferences);
    }
}
