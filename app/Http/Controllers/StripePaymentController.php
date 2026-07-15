<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        // 1. Set your secret key from your .env file
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // 2. Create a PaymentIntent with the amount and currency
            $paymentIntent = PaymentIntent::create([
                'amount' => 1000, // Amount in cents ($10.00)
                'currency' => 'usd',
            ]);

            // 3. Send the client secret back to your frontend
            return response()->json([
                'clientSecret' => $paymentIntent->client_secret
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}