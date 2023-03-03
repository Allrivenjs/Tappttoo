<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        dd($this);
        $userAuth = auth()->guard('api')?->user();
        return array_merge(parent::toArray($request), [
            'likedByMe'=> $userAuth ? $this->liked($userAuth->getAuthIdentifier()) : false,
            'roles'=> $this->user?->getRoleNames(),
        ]);
    }
}
