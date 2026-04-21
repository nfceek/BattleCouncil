<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../../config/stripe.php';

\Stripe\Stripe::setApiKey($config['secret_key']);

header('Content-Type: application/json');

try {

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'mode' => 'payment',

        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Tavern Coins Pack',
                ],
                'unit_amount' => 500, // $5.00
            ],
            'quantity' => 1,
        ]],

        'success_url' => 'http://localhost/success.php',
        'cancel_url' => 'http://localhost/cancel.php',
    ]);

    echo json_encode(['url' => $session->url]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}