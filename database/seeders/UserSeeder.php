<?php

namespace Database\Seeders;

use App\Http\Controllers\User\UserController;
use App\Models\Tattoo_artist;
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
        $user= User::query()->create([
            'name' => 'Admin',
            'lastname' => 'Ruiz',
            'phone' => '0007',
            'email' => 'admin@gmail.com',
            'profile_photo_path' => 'https://www.w3schools.com/howto/img_avatar.png',
            'password' => '$2a$10$7oMxkBuQ0PpbVxpJl0ufNerj0TTuZmRxrD76LlyKCaMCh8bpZqVS2',   //admin
        ]);
        (new UserController)->createTattooArtist($user, Tattoo_artist::factory()->make()->toArray());
        $user->assignRole('admin')->assignRole('moderator')->assignRole('tattoo_artist');

        $user2 = User::query()->create([
            'name' => 'Sebastian',
            'lastname' => 'Ricardo',
            'phone' => '0001',
            'email' => 'sebabalcar18@gmail.com',
            'profile_photo_path' => 'https://www.w3schools.com/howto/img_avatar.png',
            'password' => '$2a$10$7oMxkBuQ0PpbVxpJl0ufNerj0TTuZmRxrD76LlyKCaMCh8bpZqVS2',   //admin
        ]);
        (new UserController)->createTattooArtist($user2, Tattoo_artist::factory()->make()->toArray());
        $user2->assignRole('tattoo_artist');

        $user3 = User::query()->create([
            'name' => 'Sebastian',
            'lastname' => 'Ricardo',
            'phone' => '0001',
            'email' => 'sebabalcar18@gmail.com',
            'profile_photo_path' => 'https://www.w3schools.com/howto/img_avatar.png',
            'password' => '$2a$10$7oMxkBuQ0PpbVxpJl0ufNerj0TTuZmRxrD76LlyKCaMCh8bpZqVS2',   //admin
        ]);
        (new UserController)->createTattooArtist($user3, Tattoo_artist::factory()->make()->toArray());
        $user3->assignRole('admin')->assignRole('moderator');


//        User::factory(4)
//            ->afterCreating(function (User $user) {
//                $user->preferences()->attach([
//                    rand(1, 3),
//                    rand(4, 7),
//                ]);
//                (new UserController)->createTattooArtist($user, Tattoo_artist::factory()->make()->toArray());
//            })
//            ->create([
//                'city_id' => 243,
//            ]);
    }
}
