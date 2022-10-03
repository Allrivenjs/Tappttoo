<?php

namespace App\Http\Resources;

use App\Http\Controllers\Posts\PostController;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $auth = Auth::guard('api')->user();
        return array_merge(parent::toArray($request), [
            'roles' => $this->roles->pluck('name'),
            'posts' => route('posts-by-user', $this->id),
            'isFollowing' => $auth ? $this->isFollowing($auth) : false,
            'followers'=>[
                'data'=>$this->followers,
                'count'=>$this->followers->count()
            ],
            'followings'=>[
                'data'=>$this->followings->pluck('user'),
                'count'=>$this->followings->count()
            ],
            'city' => [
                'id'=>$this->city->id,
                'name'=>$this->city->name,
                'state'=>$this->city->state
            ]
        ]);
    }
}
