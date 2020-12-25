<?php

namespace Tests\Feature\Http\Controllers;

use App\Exceptions\PaymentGatewayChargeException;
use App\Mail\OrderConfirmation;
use App\Order;
use App\Product;
use App\Services\PaymentGateway;
use App\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Stripe\Card;
use Stripe\Exception\CardException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     */
    public function store_charges_for_order_and_creates_account()
    {
        $this->withoutExceptionHandling();
        $token = $this->faker->md5;
        $email = $this->faker->safeEmail;
        $charge_id = $this->faker->md5;


        $product = factory(Product::class)->create();

        // while we use stripe, we don't own the stripe code..
        // so we don't want to mock it
        // but we still need to write this test...
        // refactor the order controller by using push it to the boundary strategy
        // create services/paymentgateway from the controller code and mock it

        $paymentGateway = $this->mock(PaymentGateway::class);
        $paymentGateway->shouldReceive('charge')
            ->with($token, \Mockery::on(function ($argument) use ($product) {
                return $argument->product_id == $product->id
                    && $argument->total == $product->pric;
            }))
            ->andReturn($charge_id);

        $response = $this->post(route('order.store'), [
            'product_id' => $product->id,
            'stripeToken' => $token,
            'stripeEmail' => $email,
        ]);

        $response->assertRedirect('/users/edit');

        // ensure saved in the database
        $users = User::where('email', $email)->get();
        $this->assertSame(1, $users->count());

        $user = $users->first();
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('orders', [
            'product_id' => $product->id,
            'total' => $product->price,
            'user_id' => $user->id,
            'stripe_id' => $charge_id
        ]);

        // event is fired
        $event = Event::fake();
        //$event->assertDispatched('order.placed'); // this also works but this assertion should be tightened
        $order = Order::where('stripe_id', $charge_id)->first();
        $event->assertDispatched('order.placed', function($event, $argument) use($order){
            return $argument->is($order);
        });

        // mail was sent to user
        $mail = Mail::fake(OrderConfirmation::class);
        $mail->assertSend(OrderConfirmation::class, function($mail) use ($order, $user){
            return $mail->order->is($order) && $mail->hasTo($user->email);
        });

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

    /** @test */
    public function store_applies_coupon_to_order()
    {
        self::markTestIncomplete();
    }
}
