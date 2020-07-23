<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Gate;

class PermissionsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all the defined gates into the permissions table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('This will create new and update all existing permissions where changed. Do you wish to continue?')) {
            $permissions = [];

            $permissionsDescriptions = [
                'index-user' => 'See a overview of all created users',
                'viewAny-user' => 'View any user',
                'view-user' => 'View a specific user',
                'update-user' => 'Update a user',
                'delete-user' => 'Soft delete a user',
                'restore-user' => 'Restore a soft deleted user',
                'forceDelete-user' => 'Permenant delete a user',
                'confirm-user' => 'Confirm a user, so he can login.',
                'ban-user' => 'Ban a user',
            ];

            foreach (Gate::abilities() as $name => $gate) {
                if (isset($permissions[$name])) {
                    throw new \Exception("Permission $name already exists... (Exception from " . get_class() . ")");
                }elseif (!isset($permissionsDescriptions[$name])) {
                    throw new \Exception("Permission description for $name does not exist... (Exception from " . get_class() . ")");
                }
                $permissions[$name] = $name; 
            }

            DB::beginTransaction();

            foreach($permissions as $permission) {
                $newPermission = \App\Permission::firstOrCreate(['name' => $permission],
                [
                    'description' => $permissionsDescriptions[$permission],
                ]);
            }

            DB::commit();

            $headers = ['Permission', 'Description'];

            $permissions = \App\Permission::all(['name', 'description'])->toArray();

            $this->table($headers, $permissions);
        }
        
    }
}
