<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{

    public function toggleFollow(Request $request,$user): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $user1 = $this->authApi()->user();
        $user = User::query()->find($user);
        abort_unless((bool)$user,Response::HTTP_NOT_FOUND, 'User not found');
        $user1->toggleFollow($user);
        return response(null);
    }

}