<?php

namespace Http\Controllers\Posts;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    public function testDestroy()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->create();
        $this->actingAs(User::factory()->makeOne())->delete(route('posts.destroy', $post->id))
            ->assertStatus(Response::HTTP_OK);
        $this->assertNull(Post::query()->find($post->id));
    }

    public function testShow()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->create();
        $response = $this->actingAs(User::factory()->makeOne())->get("api/posts/{$post->id}")
            ->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn (AssertableJson $json) => $json->where('slug', $post->slug)->etc());
        $post->delete();
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->create();
        $this->actingAs(User::factory()->makeOne())->put("api/posts/{$post->id}", [
            'body' => 'test',
        ])->assertStatus(Response::HTTP_OK);
        $this->assertEquals('test', Post::query()->find($post->id)->body);
        $post->delete();
    }

    public function testStore()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->createOne();
        $topic = Topic::factory()->createOne();
        $response = $this->actingAs($user)->post(route('posts.store', [
            'body' => 'test',
            'topics' => [$topic->id],
        ]))->assertStatus(Response::HTTP_CREATED);
        $response->assertJson(fn (AssertableJson $json) => $json->where('body', 'test')->etc());
        $user->delete();
        $topic->delete();
    }

    public function testIndex()
    {
        $this->withoutExceptionHandling();
        $this->get(route('posts.index'))->assertStatus(Response::HTTP_OK);
    }
}
