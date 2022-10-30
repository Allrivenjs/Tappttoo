<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->except(['show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Http\Response
    {
        return response(
          Post::query()
          ->with([
              'user',
              'comments_lasted'=> [ 'replies', 'owner' ],
              'likeCounter',
              'topics',
              'images',
              'taggableUsers',
          ])->whereHas('topics', function (Builder $query) {
              $query->whereIn('name', array_merge($this->authApi()->user()->preferences()->pluck('name')->toArray(), Topic::all()->pluck('name')->random()->take(1)->toArray()));
          })->orderByDesc('created_at')
              ->groupBy('id')
              ->simplePaginate(10)
        );
    }

    public function getPostsByUser($user): \Illuminate\Http\Response
    {
        return response(Post::with([
            'user',
            'comments',
            'likeCounter',
            'images',
            'topics',
            'taggableUsers',
        ])->where('user_id', $user)->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
//        dd($request->all());
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

        return response($post->load(['topics','taggableUsers','images']))->setStatusCode(Response::HTTP_CREATED);
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
            'comments' => ['owner'],
            'user',
            'topics',
            'likeCounter',
            'images',
            'taggableUsers',
        ])));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post): \Illuminate\Http\Response
    {
        $validate = $request->validate(self::rules());
        $post->update([
            'body' => $validate['body'],
        ]);
        $post->topics()->sync($request->input('topics'));
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
