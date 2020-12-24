<?php

namespace App\Services;

use App\Order;
use Stripe\Charge;
use Stripe\Stripe;

class PaymentGateway {
    public function charge(string $token, Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
$request = request();
        $charge = Charge::create([
            "amount" => $order->totalInCents(),
            "currency" => "usd",
            "source" => $token,
            "description" => "Confident Laravel - " . $order->product->name,
            "receipt_email" => $request->get('stripeEmail')
        ]);

        return $charge->id;
    }
}
