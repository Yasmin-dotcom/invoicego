<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['name' => 'Free'],
            [
                'price' => 0,
                'invoice_limit' => 3,
                'features' => [],
                'is_active' => true,
            ]
        );

        Plan::updateOrCreate(
            ['name' => 'Pro'],
            [
                'price' => 19900,
                'invoice_limit' => null, // unlimited
                'features' => [],
                'is_active' => true,
            ]
        );
    }
}

