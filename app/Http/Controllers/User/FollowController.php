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
        $user = User::query()->findOrFail($user);
        $user1->toggleFollow($user);
        return response(null);
    }

    public function followings(User $user): \Illuminate\Http\JsonResponse
    {
        $followings = $user->followings()->with(['user'])->paginate(100);
        return FollowResourcer::collection($followings)->response();
    }

    public function followers(User $user): \Illuminate\Http\JsonResponse
    {
        $followers = $user->followers()->paginate(100);
        return FollowResourcer::collection($followers)->response();
    }

    public function randomArtist(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        return response(User::query()->with([
            'roles',
            'socialAccounts',
            'preferences',
            "city" => ['state'],
            'followers',
            'tattoo_artist',
            'followings'=> ['user'],
        ])->whereHas('roles', function ($q){
            $q->where('name', 'tattoo_artist');
        })->inRandomOrder()->take(10)->get());
    }
}
