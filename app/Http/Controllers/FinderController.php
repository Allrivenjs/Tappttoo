<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\City;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Throwable;

class FinderController extends Controller
{
    public const searchables = [
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
                "state" => ["country"]
            ],
        ],
    ];

    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory
     * @throws Throwable
     */
    public function index(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $searchables = self::searchables;
        $search = $request->get("search");
        $type = $request->get("type");
        $results = [];
        throw_if(!isset($search) || !isset($type), "search and type are required");
        if ($search && $type) {
            $searchable = $searchables[$type];
            $model = $searchable["model"];
            $fields = $searchable["fields"];
            $relations = $searchable["relations"] ?? [];
            $results = $model::where(function ($query) use ($fields, $search) {
                foreach ($fields as $field) {
                    $query->orWhere($field, "like", "%$search%");
                }
            })->with($relations)->paginate(20);
        }
        return response($results);
    }
    public const relationsDefault = [
        "topics",
        "images",
        "taggableUsers",
        'likeCounter',
    ];
    public const propertiesShow = [
        "cities" => [
            "model" => [
                "user" => "city",
            ] ,
            "relationByPost" => [
                "user" => [
                        "city" => [
                                "state" => [
                                        "country"
                                ]
                        ]
                ],
                ...self::relationsDefault
            ],
        ],
        "tattoo" => [
            "model" => "topics",
            "relationByPost" => [
                "topics" => [
                    "tattooArtists" => [
                        "user"=> [
                            "city" => [
                                "state" => [
                                    "country"
                                ]
                            ]
                        ],
                    ]
                ],
                ...self::relationsDefault
            ],
        ],
        "artist" => [
            "model" => "user",
            "relationByPost" => [
                "user" => [
                    "tattoo_artist" => [
                        "city" => [
                            "state" => [
                                "country"
                            ]
                        ]
                    ]
                ],
                ...self::relationsDefault
            ],
        ],
    ];

    /**
     * @throws Throwable
     */
    public function showPostsByType(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $type = $request->get("type");
        $id = $request->get("id");
        throw_if(!isset($type) || !isset($id), "id and type are required");
        throw_if(in_array($type,self::propertiesShow ), "type not found");
        $properties = self::propertiesShow[$type];
        $model = $properties["model"];
        $posts = Post::query();
        if (is_array($model)) {
            $key = key($model);
            $posts->whereHas($key, function (Builder $query) use ($model, $id, $key) {
                $schema = Str::plural($model[$key]);
                $query->join(
                    $schema,
                    $schema . ".id",
                    "=",
                    Str::plural($key) . "." . $model[$key] . "_id"
                )->where($schema . ".id", $id);
            });
        } else {
            $posts->whereHas($model, function ($query) use ($id, $model) {
                $model = Str::plural($model);
                $query->where("$model.id", $id);
            });
        }
        $relationByPost = $properties["relationByPost"] ?? [];
        $posts = $posts->with($relationByPost)->paginate(20);
        return response(PostResource::collection($posts));
    }
}
