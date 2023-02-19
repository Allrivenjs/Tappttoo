<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public const relations = [
        'user',
        'likeCounter',
        'topics',
        'images',
        'taggableUsers',
    ];
    public function __construct()
    {
        $this->middleware('auth:api')->except(['show','index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $posts = Post::query()
            ->with([
                ...self::relations,
                'comments_lasted'=> [ 'replies', 'owner' ]
            ])->whereHas('topics', function (Builder $query) {
                $mypreferences = $this->authApi()->user()?->preferences()->pluck('name')->toArray();
                $ramdomPreferens = Topic::all()->whereNotIn('name', $mypreferences )->random(2)->pluck('name')->toArray();
                $query->whereIn('name', array_merge($mypreferences ?? [], $ramdomPreferens ?? []));
            })->orderByDesc('created_at')->simplePaginate(10);
        dd($posts->setCollection(PostResource::collection($posts->getCollection())->collection));
        return PostResource::collection($posts);*
    }

    public function getPostsByUser($user): JsonResponse
    {
        return (PostResource::collection(
            Post::with([
                ...self::relations,
                'comments_lasted'=> [ 'replies', 'owner' ]
            ])->where('user_id', $user)->paginate(10)
        ))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate(self::rules());
        $post = Post::query()->create([
            'body' => $request->input('body'),
            'slug' => Str::uuid() . Carbon::today()->toString(),
            'user_id' => $this->authApi()->user()->id,
        ]);
        foreach ($request->file('images') as $image) {
            $post->images()->create([
                'url' => $this->uploadFile('public', $image, 'posts/'.$this->authApi()->user()->id.'/images'),
                'type' => 'public',
            ]);
        }
        $post->topics()->attach($request->input('topics'));
        $post->taggableUsers()->attach($request->input('taggableUsers'));

        return (new PostResource($post->load(['topics','taggableUsers','images'])))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return PostResource
     */
    public function show(Post $post): PostResource
    {
        return (new PostResource($post->load([
            ...self::relations,
            'comments' => [ 'replies', 'owner' ],
        ])));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post): \Illuminate\Http\Response
    {
        $rules = self::rules();
        $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        $validate = $request->validate($rules);
        $post->update([
            'body' => $validate['body'],
        ]);
        if ($request->hasFile('images')) {
            if ($post->images()->exists()) {
                $post->images()->delete();
            }
            foreach ($request->file('images') as $image) {
                $post->images()->create([
                    'url' => $this->uploadFile('public', $image, 'posts/'.$this->authApi()->user()->id.'/images'),
                    'type' => 'public',
                ]);
            }
        }
        $post->topics()->sync($request->input('topics'));
        $post->taggableUsers()->sync($request->input('taggableUsers'));
        return response(null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post): \Illuminate\Http\Response
    {
        if ($post->images()->exists()) {
            $post->images()->get()->each(function ($image) {
                Storage::disk($image->type)->delete($image->url);
                $image->delete();
            });
        }
        $post->topics()->detach();
        $post->taggableUsers()->detach();
        $post->delete();
        return response(null);
    }

    protected static function rules(): array
    {
        return [
            'body' => 'required|string',
            'topics' => 'array|required|min:1|max:3',
            'topics.*' => 'integer|exists:topics,id',
            'images' => 'array|required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|required',
            'taggableUsers' => 'array',
            'taggableUsers.*' => 'integer|exists:users,id',
        ];
    }
}
