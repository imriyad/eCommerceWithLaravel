<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Create PaymentIntent
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // Stripe requires cents
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'success' => true,
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error("PaymentIntent Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'PaymentIntent creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function handleWebhook(Request $request)
    {        

        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    Log::info("✅ Payment succeeded for intent: " . $paymentIntent->id);
                    // TODO: Update order status in DB -> "paid"
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $error = $paymentIntent->last_payment_error
                        ? $paymentIntent->last_payment_error->message
                        : 'Unknown error';
                    Log::warning("❌ Payment failed: " . $error);
                    // TODO: Update order status in DB -> "failed"
                    break;

                default:
                    Log::info("Unhandled event type: " . $event->type);
            }

            return response()->json(['status' => 'success']);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
}
