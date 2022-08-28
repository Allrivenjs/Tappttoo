<?php

namespace Http\Controllers\Comment;

use App\Http\Controllers\Comment\CommentController;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;


class CommentControllerTest extends TestCase
{

    public function testComment()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $this->actingAs($user)->post(route('comment'), [
            'body' => 'Test comment',
            'own_id' => $user->id,
            'post_id' => $post->id,
        ])->assertStatus(Response::HTTP_CREATED);
        $user->delete();
        $post->comments()->each(fn ($comment) => $comment->delete());
        $post->delete();

    }

    public function testReply()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create([
            'body' => 'Test comment',
            'own_id' => $user->id,
        ]);
        $this->actingAs($user)->post(route('comment.reply'), [
            'body' => 'Test reply',
            'parent_id' => $comment->id,
            'own_id' => $user->id,
            'post_id' => $post->id,
        ])->assertStatus(Response::HTTP_OK);
        $user->delete();
        $post->comments()->each(function ($comment){
            $comment->replies()->each(fn ($reply) => $reply->delete());
            $comment->delete();
        });
        $post->delete();
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create([
            'body' => 'Test comment',
            'own_id' => $user->id,
        ]);
        $this->actingAs($user)->delete(route('comment.delete', $comment->id))->assertStatus(Response::HTTP_OK);
        $post->delete();
    }

    public function testGetComments()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->make();
        $postc = Post::factory()->create();
        $post = Post::query()->first();
        $this->actingAs($user)->call('GET',route('comment.get'),['post_id' => $post->id])
            ->assertJson($post->comments()->with(['replies', 'owner'])->get()->toArray());
        $postc->delete();
    }
}
