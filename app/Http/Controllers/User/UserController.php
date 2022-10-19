<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): \Illuminate\Http\JsonResponse
    {
        return (new UserResource($user->load([
            'roles',
            'socialAccounts',
            'preferences',
            "city" => ['state'],
            'followers',
            'tattoo_artist',
            'followings'=> ['user'],
        ])))->response();
    }


    public function mePosts(): \Illuminate\Http\JsonResponse
    {
        return (new PostResource(auth()->user()->with(
            [
                'posts'=> [
                    'comments' => ['user'],
                    'topics',
                    'likes' => ['user'],
                ],
                'tattoo_artist',
            ]
        )))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->rules());
        $user->update($validate);
        $this->updateBiography($request, $user);
        !$request->input('tattoo_artist_bool') ?: $this->createTattooArtist($user) ;
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    protected function rules(): array
    {
        return  [
            'name' => 'required|string',
            'lastname' => 'required|string',
            'phone' => 'required|string',
            'locate_maps' => 'required|string',
            'city_id' => 'required|integer',
            'address' => 'required|string',
            'tattoo_artist_bool' => 'required|boolean',
        ];
    }

    protected function ruleByBiography(): array
    {
        return  [
            'biography' => 'required|string|max:150',
        ];
    }

    public function createTattooArtist(User $user, $data=[])
    {
        $user->tattoo_artist()->create($data);
    }

    public function updateBiography(Request $request, User $user): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->ruleByBiography());
        $user->update($validate);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
