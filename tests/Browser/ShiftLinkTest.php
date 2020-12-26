<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShiftLinkTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * @test
     */
    public function it_links_to_laravel_shift_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Laravel Shift')
            ->clickLink('Laravel Shift')
            ->assertUrlIs('https://laravelshift.com/');
        });
    }
}
