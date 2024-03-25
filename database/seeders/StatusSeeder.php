<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => "active", 'code' => "active", 'style' => "success", 'is_default' => 1],
            ['name' => "inactive", 'code' => "inactive", 'style' => "danger", 'is_default' => 0]
        ];
        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}
