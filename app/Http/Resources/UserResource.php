<?php

namespace App\Http\Resources;

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
        $auth = Auth::guard('api')?->user();
        return array_merge(parent::toArray($request), [
            'roles' => $this->getRoleNames(),
            'posts' => route('posts-by-user', $this->id),
            'is_following' => $auth && $this->followers->find($auth->id),
            'followers'=>[
                'data'=>$this->followers,
                'count'=>$this->followers->count()
            ],
            'followings'=>[
                'data'=>$this->followings->pluck('user'),
                'count'=>$this->followings->count()
            ],
            'city' => [
                'id'=>$this->city->id??null,
                'name'=>$this->city->name??null,
                'state'=>$this->city->state??null
            ],
            'subscription_active' => $this->getSubscriptionActive(),
        ]);
    }
}
