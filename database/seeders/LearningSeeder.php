<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Module;
use Illuminate\Database\Seeder;

class LearningSeeder extends Seeder
{
    public function run(): void
    {
        Batch::firstOrCreate(['name' => 'November Batch'], ['is_active' => true]);
        Batch::firstOrCreate(['name' => 'December Batch'], ['is_active' => true]);

        $modules = [
            ['name' => 'Intraday', 'timeframe' => '1 min, 5 min', 'description' => 'Short-term trades within a single day.', 'sort_order' => 1],
            ['name' => 'Swing', 'timeframe' => '15 min – 2 hour', 'description' => 'Trades lasting a few days based on swings.', 'sort_order' => 2],
            ['name' => 'Short Term', 'timeframe' => '15 trading days', 'description' => 'Short-term positional trades.', 'sort_order' => 3],
            ['name' => 'Medium Term', 'timeframe' => '1–3 months', 'description' => 'Medium-term investment horizon.', 'sort_order' => 4],
            ['name' => 'Long Term', 'timeframe' => '1–2 years', 'description' => 'Long-term investing strategies.', 'sort_order' => 5],
        ];

        foreach ($modules as $module) {
            Module::firstOrCreate(
                ['name' => $module['name']],
                [
                    'timeframe' => $module['timeframe'],
                    'description' => $module['description'],
                    'sort_order' => $module['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
