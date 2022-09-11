<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
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
        ])->get());
    }

    public function getPostsByUser($user): \Illuminate\Http\Response
    {
        return response(Post::with([
            'user',
            'comments',
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
        dd($this->authWeb()->user(), $this->authApiaa()->user());
        $post = Post::create([
            'body' => $request->input('body'),
            'slug' => fake()->slug().'-'.now(),
            'user_id' => $this->authWeb()->user()->id,
        ]);
        $post->topics()->attach($request->input('topics'));

        return response($post->load('topics'))->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post): \Illuminate\Http\Response
    {
        return response($post);
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
