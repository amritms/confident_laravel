<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\WithFaker;
use ProductsTableSeeder;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PurchasepackageTest extends DuskTestCase
{
    use WithFaker;
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProductsTableSeeder::class);
    }

    /**
     * @test
     */
    public function it_can_purchase_the_starter_package_and_create_account()
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSee('A step by step guide to testing your Laravel applications.')
                ->assertSee('$89')
                ->assertSee('STARTER')
                ->clickLink('$89')
                ->waitFor('iframe[name=stripe_checkout_app]')
                ->withinFrame('iframe[name=stripe_checkout_app]', function($browser){
                    $browser->pause(100);
                    $browser->assertSee('Starter');
                    $browser->keys('input[placeholder="Email"]', $this->faker->safeEmail)
                        ->keys('input[placeholder="Card number"]', '4242424242424242')
                        ->keys('input[placeholder="MM / YY"]', '01' . now()->addYear()->format('y'))
                        ->keys('input[placeholder="CVC"]', '123')
                        ->press('button[type="submit"')
                        ->waitUntilMissing('iframe[name=stripe_checkout_app]');
                })
                ->waitForReload()
            ->assertPathIs('/users/edit');
        });
    }
}
