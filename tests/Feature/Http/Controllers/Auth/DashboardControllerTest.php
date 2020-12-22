<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\User;
use App\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_retrieves_the_last_watched_video()
    {
        $video = factory(Video::class)->create();

        $user = factory(User::class)->create([
            'last_viewed_video_id' => $video->id
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('videos.show');
        $response->assertViewHas('now_playing', $video);
    }

    /** @test */
    public function it_defaults_first_video_for_a_new_user()
    {
        $video = factory(Video::class)->create();

        $user = factory(User::class)->create();

        $this->assertNull($user->fresh()->last_viewed_video_id);

        $this->actingAs($user)->get('/dashboard');

        $this->assertEquals($video->id, $user->fresh()->last_viewed_video_id);
    }
}
