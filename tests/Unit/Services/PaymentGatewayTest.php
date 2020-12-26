<?php

namespace Tests\Unit;

use App\Exceptions\PaymentGatewayChargeException;
use App\Order;
use App\Services\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function charge_colects_payment_with_stripe()
    {
        // a lot of setup

        // doesn't necessarily config "payments" work ...

        // Dusk + existing HTTP Tests = confident!
        $subject = new PaymentGateway();
        $token = $this->createTestToken();
        $order = factory(Order::class)->create();

        $actual = $subject->charge($token, $order);

        $charge = $this->getStripeCharge($actual);

        $this->assertEquals($actual, $charge->id);
        $this->assertEquals($order->totalInCents(), $charge->amount);
        $this->assertEquals('usd', $charge->currency);
        $this->assertEquals('Confident Laravel - '. $order->product->name, $charge->description);
    }

    /** @test */
    public function charge_throws_payment_gateway_exception()
    {
        $subject = new PaymentGateway();
        $order = factory(Order::class)->create();

        $this->expectException(PaymentGatewayChargeException::class);

        $subject->charge('invalid-token', $order);
    }

    private function createTestToken()
    {
        // make a call to Stripe API for a test token
        $token = \Stripe\Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 6,
                'exp_year' => now()->addYear()->format('Y'),
                'cvc' => '314'
            ]
        ], ['api_key' => config('services.stripe.secret')]);

        return $token->id;
    }

    private function getStripeCharge(string $id)
    {
        // call stripe API to get charge object
        return \Stripe\Charge::retrieve($id);
    }
}
