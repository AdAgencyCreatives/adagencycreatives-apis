<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportUserData extends Command
{
    protected $signature = 'import:users';

    protected $description = 'Import users data from JSON';

    public function handle()
    {
        $jsonFilePath = public_path('export/users.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $usersData = json_decode($jsonContents, true);

        $now = now();
        foreach ($usersData as $userData) {
            $this->createUser($userData, $now);
        }

        $this->info('User data imported successfully.');
    }

    private function createUser($userData, $now)
    {
        $user = new User();
        $user->uuid = Str::uuid();
        $user->first_name = $userData['user_meta']['first_name'][0];
        $user->last_name = $userData['user_meta']['last_name'][0];
        $user->username = $userData['user_nicename'];
        $user->email = $userData['user_email'];
        $user->password = $userData['user_pass'];
        $user->role = $this->mapUserRole($userData['user_meta']['wp_capabilities'][0]);
        $user->status = $this->mapUserStatus($userData['user_meta']['user_account_status'][0] ?? 'approved', $user);

        $userRegisteredTimestamp = strtotime($userData['user_registered']);
        $user->created_at = Carbon::createFromTimestamp($userRegisteredTimestamp);

        $user->updated_at = $now;

        try {
            $user->save();
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }

    private function mapUserRole($capability)
    {
        $capabilities = unserialize($capability);

        if (isset($capabilities['wp_job_board_pro_employer']) && $capabilities['wp_job_board_pro_employer']) {
            return 'agency';
        } elseif (isset($capabilities['wp_job_board_pro_candidate']) && $capabilities['wp_job_board_pro_candidate']) {
            return 'creative';
        }
        elseif (isset($capabilities['administrator']) && $capabilities['administrator']) {
            return 'admin';
        }

        return null; // Return null for unhandled cases
    }

    private function mapUserStatus($accountStatus, $user)
    {
        $statusMapping = [
            'approved' => 'active',  // Active status
            'denied' => 'inactive',    // Inactive status
        ];

        return $statusMapping[$accountStatus] ?? 'pending'; // Default to pending status if not found
    }
}