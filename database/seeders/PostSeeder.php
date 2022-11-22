<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::factory()->count(1000)
            ->has(Image::factory()->count(1))
            ->afterCreating(fn (Post $post) => $post->topics()->attach([rand(1, 2), rand(3, 4), rand(5, 6)]))
            ->create();

//        $data = [];
//        for ($i = 0 ; $i <= 300 ; $i++ ){
//            $data[] = [
//                'slug' => 'slug-'.$i,
//                'body' => fake()->paragraph,
//                'user_id' => 1,
//            ];
//        }
//        foreach (array_chunk($data, 1000) as $chunk) {
//            Post::insert($chunk);
//        }
    }
}
