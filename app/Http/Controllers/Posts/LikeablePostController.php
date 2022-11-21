<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikeablePostController extends Controller
{


    /**
     * @param Request $request
     * @param Post $post
     * @return Application|ResponseFactory|Response
     */
    public function like(Request $request, Post $post): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $post->like($this->authApi()->user()->getAuthIdentifier());
        return response($post->likeCount, 200);
    }

    public function unlike(Request $request, Post $post): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $post->unlike($this->authApi()->user()->getAuthIdentifier());
        return response($post->likeCount, 200);
    }

    public function countLikes(Post $post): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        return response($post->likeCount);
    }
}
