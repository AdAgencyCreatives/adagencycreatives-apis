<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RecruiterPermissionCommand extends Command
{
    protected $signature = 'adagencycreatives:recruiter-permission';

    protected $description = 'It will create all the roles and permissions';

    public function handle()
    {
        $recruiter = Role::create(['name' => 'recruiter']);

         $all_permissions = [
            'agency' => [
                'agency.create',
                'agency.update',
                'agency.delete',

                'job.create',
                'job.update',
                'job.delete',

            ]

        ];
        $recruiter->givePermissionTo($all_permissions['agency']);

        return Command::SUCCESS;
    }
}
