<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $userAuth = auth()->guard('api')?->user();
//        parent::toArray($request)
        return array_merge( $this->collection, [
            'likedByMe'=> $userAuth ? $this->liked($userAuth->getAuthIdentifier()) : false,
            'roles'=> $this->user->getRoleNames(),
        ]);
    }
}
