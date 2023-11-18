<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class UpdateUserStatus extends Command
{
    protected $signature = 'import:users-status';

    protected $description = 'Import users status from JSON';

    public function handle()
    {
        $jsonFilePath = public_path('export/users.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $usersData = json_decode($jsonContents, true);

        foreach ($usersData as $userData) {
            $this->updateUser($userData);
        }

        $this->info('User data imported successfully.');
    }

    private function updateUser($userData)
    {
        $user = User::where('email', $userData['user_email'])->first();
        if(!$user){
            return;
        }

        $status = $this->mapUserStatus($userData['user_meta']['user_account_status'][0] ?? 'approved');
        dump($status);
        $user->status = $status;
        $user->save();
    }

    private function mapUserStatus($accountStatus)
    {
        $statusMapping = [
            'approved' => 'active',  // Active status
            'denied' => 'inactive',    // Inactive status
        ];

        return $statusMapping[$accountStatus] ?? 'pending'; // Default to pending status if not found

   }
}