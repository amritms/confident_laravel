<?php

namespace Tests\Feature\Http\Controllers;

use App\Order;
use App\Product;
use App\Services\PaymentGateway;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     */
    public function testExample()
    {
        $this->withoutExceptionHandling();
        $token = $this->faker->md5;

        $product = factory(Product::class)->create();

        // while we use stripe, we don't own the stripe code..
        // so we don't want to mock it
        // but we still need to write this test...
        // refactor the order controller by using push it to the boundary strategy
        // create services/paymentgateway from the controller code and mock it

        $paymentGateway = $this->mock(PaymentGateway::class);
        $paymentGateway->shouldReceive('charge')
            ->with($token, \Mockery::type(Order::class))
            ->andReturn('...');

        $response = $this->post(route('order.store'), [
            'product_id' => $product->id,
            'stripeToken' => $token,
            'stripeEmail' => $this->faker->safeEmail,
        ]);

        $response->assertRedirect('/users/edit');

        self::markTestIncomplete();
        // ensure saved in the database
        // event is fired
        // mail was sent to user
        // user was loggedin
    }
}
