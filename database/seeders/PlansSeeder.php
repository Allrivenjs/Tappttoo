<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plant = [
            [
                'slug' => 'artist_gold',
                'price' => 30000,
                'name' => 'Artist Gold',
                'description' => 'Artist Gold',
            ],
            [
                'slug' => 'artist_silver',
                'price' => 15000,
                'name' => 'Artist Silver',
                'description' => 'Artist Silver',
            ],
            [
                'slug' => 'artist_bronze',
                'price' => 10000,
                'name' => 'Artist Bronze',
                'description' => 'Artist Bronze',
            ],
            [
                'slug' => 'artist_free',
                'price' => 0,
                'name' => 'Artist Free',
                'description' => 'Artist Free',
            ],
        ];
        foreach ($plant as $plan) {
            \App\Models\Plan::query()->create($plan);
        }
    }
}
