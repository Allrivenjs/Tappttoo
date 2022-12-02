<?php

namespace Database\Seeders;

use App\Http\Controllers\User\UserController;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'lastname' => 'Ruiz',
            'phone' => '0007',
            'email' => 'admin@gmail.com',
            'profile_photo_path' => 'https://www.w3schools.com/howto/img_avatar.png',
            'password' => '$2a$10$7oMxkBuQ0PpbVxpJl0ufNerj0TTuZmRxrD76LlyKCaMCh8bpZqVS2',   //admin
        ])->assignRole('admin')->assignRole('moderator')->assignRole('tattoo_artist');
        User::factory(4000)
            ->afterCreating(function (User $user) {
                $user->preferences()->attach([
                    rand(1, 3),
                    rand(4, 7),
                ]);
                (new UserController)->createTattooArtist($user);
            })
            ->create([
                'city_id' => 243,
            ]);
    }
}
