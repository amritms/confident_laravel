<?php

namespace Tests\Feature\Http\Controllers;

use App\Exceptions\PaymentGatewayChargeException;
use App\Order;
use App\Product;
use App\Services\PaymentGateway;
use Stripe\Card;
use Stripe\Exception\CardException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     *
     */
    public function Example()
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
            ->andReturn('charge-id');

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

    /** @test */
    public function store_returns_error_view_when_charge_fails()
    {
//        $this->withoutExceptionHandling();
        $token = $this->faker->md5;

        $product = factory(Product::class)->create();

        // while we use stripe, we don't own the stripe code..
        // so we don't want to mock it
        // but we still need to write this test...
        // refactor the order controller by using push it to the boundary strategy
        // create services/paymentgateway from the controller code and mock it

        $paymentGateway = $this->mock(PaymentGateway::class);

        // always use simplest test double possible
        $exception = new PaymentGatewayChargeException(
            'sad path order exception',
           ['error' => ['data' => 'passed to view']]
        );
        $paymentGateway->shouldReceive('charge')
            ->with($token, \Mockery::type(Order::class))
            ->andThrows($exception);

        $response = $this->post(route('order.store'), [
            'product_id' => $product->id,
            'stripeToken' => $token,
            'stripeEmail' => $this->faker->safeEmail,
        ]);

        $response->assertOk();
        $response->assertViewIs('errors.generic');
        $response->assertViewHas('template', 'partials.errors.charge_failed');
        $response->assertViewhas('data', ['data' => 'passed to view']);
    }
}
