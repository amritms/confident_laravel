<?php

namespace Tests\Unit;

use App\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use refreshDatabase;

    /** @test */
    public function hasDownload()
    {
        $video = factory(Video::class)->make();

        // $video->id = null
        $this->assertFalse($video->hasDownload());

        $video->id = 1;
        $this->assertFalse($video->hasDownload());

        $video->id = 8;
        $this->assertTrue($video->hasDownload());

        $video->id = 9;
        $this->assertTrue($video->hasDownload());
    }

    /** @test */
    public function it_orders_by_ordinal()
    {
        factory(Video::class)->create([
            'ordinal' => 90
        ]);

        factory(Video::class)->create([
            'ordinal' => 1
        ]);

        factory(Video::class)->create([
            'ordinal' => 42
        ]);

        $videos = Video::all();

        $this->assertSame(3, $videos->count());
        $this->assertEquals(1, $videos[0]->ordinal);
        $this->assertEquals(42, $videos[1]->ordinal);
        $this->assertEquals(90, $videos[2]->ordinal);

        // above 3 assertions can be combined into one line
//        $this->assertEquals([1, 42, 90], $videos->pluck('ordinal')->toArray());
    }
}
