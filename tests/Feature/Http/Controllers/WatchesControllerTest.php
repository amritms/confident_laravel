<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use App\Video;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mockery\Mock;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WatchesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * Lesson 18, confident laravel
     */
    public function store_return_a_204()
    {
//        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $video = factory(Video::class)->create();

   /*     $mock = \Mockery::mock();
        $mock->shouldReceive('info')
            ->once()
            ->with('video.watched', [$video->id]);
// ->expects() ==> shouldReceive()->once()
        // Log::swap($mock);
        */

        $event = Event::fake();

        $response = $this->actingAs($user)->post(route('watches.store'), [
            'user_id' => $user->id,
            'video_id' => $video->id
        ]);

        $response->assertStatus(204);

        $event->assertDispatched('video.watched', function($event, $arguments) use ($video) {
            $this->assertEquals([$video->id], $arguments, 'The arguments passed to the ['. $event .'] event were unexpected');

            // return $arguments == [$video->id]
            return true;
        });

        $this->assertDatabaseHas('watches', [
            'user_id' => $user->id,
            'video_id' => $video->id
        ]);
    }
}
