<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FinderController extends Controller
{
    const searchables = [
        "tattoo" => [
            "model" => Topic::class,
            "fields"=> ["name"]
        ],
        "artist" => [
            "model" => User::class,
            "fields"=>["name","lastname"],
            "relations" => [
                "tattoo_artist",
                "preferences",
                "city",
            ]
        ],
        "cities"=>[
            "model" => City::class,
            "fields"=> ["name"],
            "relations" => [
                "country"
            ],
        ],
    ];

    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory
     * @throws \Throwable
     */
    public function index(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $searchables = self::searchables;
        $search = $request->get("search");
        $type = $request->get("type");
        $results = [];
        throw_if(!isset($search) || !isset($type), "search and type are required");
        if($search && $type){
            $searchable = $searchables[$type];
            $model = $searchable["model"];
            $fields = $searchable["fields"];
            $relations = $searchable["relations"] ?? [];
            $results = $model::where(function($query) use ($fields, $search){
                foreach($fields as $field){
                    $query->orWhere($field, "like", "%$search%");
                }
            })->with($relations)->paginate(20);
        }
        return response($results);
    }
}
