<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportUserData extends Command
{
    protected $signature = 'import:users-data';

    protected $description = 'Import users data from JSON';

    public function handle()
    {
        $jsonFilePath = public_path('export/users.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $usersData = json_decode($jsonContents, true);

        foreach ($usersData as $userData) {
            $this->createUser($userData);
        }

        $this->info('User data imported successfully.');
    }

    private function createUser($userData)
    {
        $user = new User();
        $user->uuid = Str::uuid();
        $user->first_name = $userData['user_meta']['first_name'][0];
        $user->last_name = $userData['user_meta']['last_name'][0];
        $user->username = $userData['user_login'];
        $user->email = $userData['user_email'];
        $user->password = $userData['user_pass'];
        $user->role = $this->mapUserRole($userData['user_meta']['wp_capabilities'][0]);
        $user->status = $this->mapUserStatus($userData['user_meta']['user_account_status'][0] ?? 'approved');

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

        return null; // Return null for unhandled cases
    }

    private function mapUserStatus($accountStatus)
    {
        $statusMapping = [
            'approved' => 1,  // Active status
            'denied' => 2,    // Inactive status
        ];

        return $statusMapping[$accountStatus] ?? 0; // Default to pending status if not found
    }
}
