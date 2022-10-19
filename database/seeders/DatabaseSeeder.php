<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

         /*\App\Models\User::factory()->create([
             'name' => 'Test User',
             'email' => 'test@example.com',
         ]);*/

        Storage::makeDirectory('public/images');
        Storage::makeDirectory('private/images');

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PostSeeder::class,
            CountriesSeeder::class,
            TopicsSeeder::class,
        ]);

    }
}
