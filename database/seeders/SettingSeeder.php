<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Settings\Settings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class_methods = get_class_methods(Settings::class);

        foreach ($class_methods as $class_method) {
            $branch = Settings::$class_method();
            $branch->save();
            foreach ($branch->getChildren() as $child) {
                $child->save();
                $child->saveChildren();
            }
        }
    }
}
