<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testMocking()
    {
        $mock = \Mockery::mock();

        $mock->shouldReceive('foo')
            ->with('bar')
            ->andReturn('baz');

        $this->assertEquals('baz', $mock->foo('bar'));

        $mock->shouldReceive('qux')
            ->andReturnNull();
        $this->assertNull($mock->qux());
    }

    /**
     * by default spy return null (its like null object)
     * it is more forgiving
     * secretly tracks behavior
     * if expectation has not been setup, when spy receives a call, it just returns null
     *
     */
    public function testSpying()
    {
        // secretly tracks behavior

        $spy = \Mockery::spy();
        $this->assertNull($spy->qux());

        $spy->shouldHaveReceived('qux')->once();
    }
}
