<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../../config/stripe.php';

\Stripe\Stripe::setApiKey($config['secret_key']);

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$endpoint_secret = 'whsec_xxx';

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch (Exception $e) {
    http_response_code(400);
    exit();
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;

    // 🔥 THIS is where you credit the user
    // example:
    // addCoins($session->client_reference_id);

}

http_response_code(200);