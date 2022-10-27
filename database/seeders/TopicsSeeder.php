<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            "Diseños",
            "Realista",
            "Blackwork",
            "Old School",
            "Neotradicional",
            "Puntillistas",
            "Black & Gray"
        ];
        $data = [];

        foreach ($array as $item) {
            $data[] = [
                'name' => $item,
            ];
        }
        Topic::query()->insert($data);
    }
}
