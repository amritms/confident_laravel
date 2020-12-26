<?php

namespace App\Services;

use App\Exceptions\PaymentGatewayChargeException;
use App\Order;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class PaymentGateway
{
    public function charge(string $token, Order $order)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $charge = Charge::create([
                "amount" => $order->totalInCents(),
                "currency" => "usd",
                "source" => $token,
                "description" => "Confident Laravel - " . $order->product->name,
                "receipt_email" => request()->get('stripeEmail')
            ]);

            return $charge->id;
        } catch (ApiErrorException $exception) {
            throw new PaymentGatewayChargeException($exception->getMessage(), $exception->getJsonBody());
        }
    }
}
