<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Status;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create(
            ['name' => "admin",'login' => "admin",'email' => "admin@sheundani.net",'password' => bcrypt( config('app.admin_password') ), 'status_id' => Status::active()->first()->id]);

        $adminrole = Role::create(['name' => 'Admin']);

        $permissions = Permission::pluck('id','id')->all();

        $adminrole->syncPermissions($permissions);
        $adminrole->revokePermissionTo('permission-create');
        $adminrole->revokePermissionTo('permission-update');
        $adminrole->revokePermissionTo('permission-delete');

        $user->assignRole([$adminrole->id]);
    }
}
