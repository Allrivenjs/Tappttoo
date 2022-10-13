<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Http\Response
    {
        return response(Post::with([
            'user',
            'comments',
            'likeCounter',
        ])->get());
    }

    public function getPostsByUser($user): \Illuminate\Http\Response
    {
        return response(Post::with([
            'user',
            'comments',
            'likeCounter',
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
        $request->validate(self::rules());
        $post = Post::create([
            'body' => $request->input('body'),
            'slug' => fake()->slug().'-'.now(),
            'user_id' => $this->authApi()->user()->id,
        ]);
        $post->topics()->attach($request->input('topics'));

        return response($post->load('topics'))->setStatusCode(Response::HTTP_CREATED);
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
            'comments_lasted' => ['owner'],
          //  'likes' => ['user_take_five'],
            'user',
            'topics',
            'likeCounter',
        ])));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): \Illuminate\Http\Response
    {
        $validate = $request->validate(self::rules());
        $post = Post::query()->findOrFail($id);
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
        $post->delete();

        return response(null);
    }

    protected static function rules(): array
    {
        return [
            'body' => 'required|string',
            'topics' => 'array',
        ];
    }
}
