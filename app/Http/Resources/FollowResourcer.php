<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Overtrue\LaravelFollow\Followable;

class FollowResourcer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = auth()->guard('api')?->user();
        $followable = $this->followable;
        $data = parent::toArray($request);
       if ($user) {
           if ($followable){
               $data['is_following'] = $user->isFollowing($followable);
               $data['followable'] = $followable;
           }
           if (!$followable){
               $data['is_following'] = $this?->isFollowing($user);
           }
        }
        return $data;
    }
}
