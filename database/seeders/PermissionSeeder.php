<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Auth\Permissions;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class_methods = get_class_methods(Permissions::class);

        foreach ($class_methods as $class_method) {
            $permissions = Permissions::$class_method()->getAllPermissions();
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission[0], 'level' => $permission[1]]);
            }
        }
    }
}
