<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TopicController extends Controller
{
    public function __construct()
    {
//        $this->middleware('can:taskAdmin.task')->except('index', 'show');
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Http\Response
    {
        return response(Topic::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        $request->validate([
            'name'=>'required',
        ]);
        Topic::create([
            'name' => $request->input('name'),
        ]);

        return response(null)->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function show(Topic $topic): \Illuminate\Http\Response
    {
        return response($topic);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Topic $topic): \Illuminate\Http\Response
    {
        $request->validate([
            'name'=>'required',
        ]);
        $topic->update([
            'name' => $request->input('name'),
        ]);

        return response(null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Topic $topic): \Illuminate\Http\Response
    {
        $topic->delete();

        return response(null);
    }
}
