<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\FollowResourcer;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{
    public function toggleFollow(Request $request, $user): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $user1 = $this->authApi()->user();
        $user = User::query()->find($user);
        abort_unless((bool)$user, Response::HTTP_NOT_FOUND, 'User not found');
        $user1->toggleFollow($user);
        return response(null);
    }

    public function followings(User $user): \Illuminate\Http\JsonResponse
    {
        $followings = $user->followings()->paginate(100);
        return FollowResourcer::collection($followings)->response();
    }

    public function followers(User $user): \Illuminate\Http\JsonResponse
    {
        $followers = $user->followers()->paginate(100);

        return FollowResourcer::collection($followers)->response();
    }
}
