<?php

namespace Tests\Unit;

use App\Services\PaymentGateway;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    /** @test */
    public function charge()
    {
        // a lot of setup
        // doesn't necessarily configm "payments" work ...

        // Dusk + existing HTTP Tests = confident!
        $subject = new PaymentGateway();
        $token = $this->createTestToken();
        $order = new Order();

        $actual = $subject->charge($token, $order);

        $charge = $this->getStripeCharge($actual);

        $this->assertEqual($charge->total = $order->total);
    }

    private function createTestToken()
    {
        // make a call to Stripe API for a test token
    }

//    private function
}
