<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionCommand extends Command
{
    protected $signature = 'adagencycreatives:permission';

    protected $description = 'It will create all the roles and permissions';

    public function handle()
    {
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $admin = Role::create(['name' => 'admin']);
        $advisor = Role::create(['name' => 'advisor']);
        $agency = Role::create(['name' => 'agency']);
        $creative = Role::create(['name' => 'creative']);

        $all_permissions = [
            'agency' => [
                'agency.create',
                'agency.update',
                'agency.delete',

                'job.create',
                'job.update',
                'job.delete',

            ],
            'creative' => [
                'creative.create',
                'creative.update',
                'creative.delete',

                'application.create',
                'application.update',
                'application.delete',

                'resume.create',
                'resume.update',
                'resume.delete',

                'education.create',
                'education.update',
                'education.delete',

                'experience.create',
                'experience.update',
                'experience.delete',
            ],

        ];

        foreach ($all_permissions as $key => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
            }
        }

        $advisor->givePermissionTo($all_permissions['agency']);
        $advisor->givePermissionTo($all_permissions['creative']);

        $agency->givePermissionTo($all_permissions['agency']);
        $creative->givePermissionTo($all_permissions['creative']);

        return Command::SUCCESS;
    }
}
