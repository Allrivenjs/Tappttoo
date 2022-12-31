<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

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
        if ($followable) {
            if ($user) $data['is_following'] = $user->isFollowing($followable);
            $data['followable'] = $followable;
            $data['roles'] = $this->followable->getRoleNames();
        }
        if (!$followable) {
            if ($user) $data['is_following'] = $this?->isFollowing($user);
            $data['roles'] = $this->getRoleNames();
        }
        return $data;
    }
}
