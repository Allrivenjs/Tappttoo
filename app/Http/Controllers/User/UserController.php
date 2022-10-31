<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Tattoo_artist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                    'likeCounter',
                    'images',
                    'taggableUsers',
                ],
                'tattoo_artist',
            ]
        )))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): \Illuminate\Http\Response
    {
        $user = $this->authApi()->user();
        $validate = $request->validate($this->rules());
        $user->update($validate);
        $this->updateBiography($request, $user);
        !$request->input('tattoo_artist_bool') ?: $this->createTattooArtist($user);
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

    public function createTattooArtist(User|\Illuminate\Contracts\Auth\Authenticatable|null $user, $data=[])
    {
        Tattoo_artist::query()->create(array_merge($data, ['user_id' => $user->id]));
        $user->assignRole('tattoo_artist');
    }

    public function updateBiography(Request $request, User|\Illuminate\Contracts\Auth\Authenticatable|null $user): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->ruleByBiography());
        $user->update($validate);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function assignPreferences(Request $request): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->rulesPreferences());
        $user = $this->authApi()->user();
        $user->preferences()->sync($validate['preferences']);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }


    private function rulesPreferences(): array
    {
        return [
            'preferences' => 'required|array',
            'preferences.*' => 'required|integer|exists:topics,id',
        ];
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
