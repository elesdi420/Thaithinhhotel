<?php
/**
 * Test script for Story 2.2: End-to-end VietQR Webhook Reconciliation
 */

define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';

echo "=== START TEST: WEBHOOK RECONCILIATION (STORY 2.2) ===\n";

// 1. Ensure test customer exists
$testEmail = 'guest.webhook@example.com';
$idCustomer = (int) Db::getInstance()->getValue("SELECT id_customer FROM " . _DB_PREFIX_ . "customer WHERE email = '" . pSQL($testEmail) . "'");
$customer = new Customer($idCustomer ? $idCustomer : null);
if (!$customer->id) {
    $customer->firstname = 'Nguyễn';
    $customer->lastname = 'Văn Khách';
    $customer->email = $testEmail;
    $customer->passwd = (new PasswordHashing())->passwordHash('Password123!');
    $customer->active = 1;
    $customer->save();
}
echo "1. Test Customer: ID {$customer->id} ({$customer->email})\n";

// 2. Create a fresh test Order in Pending status (QLO_OS_VIETQR_PENDING = 13)
$pendingStateId = (int) Configuration::get('QLO_OS_VIETQR_PENDING');
if (!$pendingStateId) {
    $pendingStateId = 13;
}

$totalAmount = 1600000.00;
$order = new Order();
$order->id_carrier = 0;
$order->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
$order->id_customer = $customer->id;
$order->id_cart = 1;
$order->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
$order->id_address_delivery = 1;
$order->id_address_invoice = 1;
$order->id_address_tax = 1;
$order->current_state = $pendingStateId;
$order->secure_key = $customer->secure_key;
$order->payment = 'VietQR Bank Transfer';
$order->module = 'qlovietqr';
$order->total_paid = $totalAmount;
$order->total_paid_real = 0;
$order->total_products = $totalAmount;
$order->total_products_wt = $totalAmount;
$order->conversion_rate = 1;
$order->reference = Order::generateReference();
$order->save();

$orderId = (int) $order->id;
$orderRef = $order->reference;

// Set initial order history
$initHistory = new OrderHistory();
$initHistory->id_order = $orderId;
$initHistory->changeIdOrderState($pendingStateId, $orderId);
$initHistory->add();

echo "2. Created Pending Booking Order:\n";
echo "   - Order ID: {$orderId}\n";
echo "   - Reference: {$orderRef}\n";
echo "   - Total Amount: " . number_format($totalAmount) . " VND\n";
echo "   - Current State: {$pendingStateId} (Chờ thanh toán VietQR)\n";

// 3. Send Webhook POST to endpoint
$secret = Configuration::get('VIETQR_WEBHOOK_SECRET');
$webhookUrl = "http://127.0.0.1/index.php?fc=module&module=qlovietqr&controller=webhook&token=" . $secret;

$payload = [
    'content' => 'HOTEL ' . $orderRef,
    'amount' => $totalAmount,
    'transaction_id' => 'TXN_' . time(),
    'reference' => $orderRef
];

echo "\n3. Sending Webhook POST with payload:\n";
echo "   " . json_encode($payload) . "\n";

$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Host: localhost:8080'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Webhook Response (HTTP {$httpCode}):\n";
echo "   " . $response . "\n";

$resData = json_decode($response, true);
if ($httpCode !== 200 || !isset($resData['status']) || $resData['status'] !== 'success') {
    echo "ERROR: Webhook did not return success!\n";
    exit(1);
}

// 4. Verify Database State Transition
$updatedOrder = new Order($orderId);
$currentState = (int) $updatedOrder->getCurrentState();
$expectedState = (int) Configuration::get('PS_OS_PAYMENT_ACCEPTED'); // 2

echo "\n4. Verifying State in MariaDB:\n";
echo "   - Order ID: {$orderId}\n";
echo "   - Previous State: {$pendingStateId}\n";
echo "   - Current State: {$currentState} (Expected: {$expectedState} - Payment accepted)\n";

if ($currentState === $expectedState) {
    echo "   ==> Database State Check: PASS!\n";
} else {
    echo "   ==> Database State Check: FAILED!\n";
    exit(1);
}

// 5. Test Idempotency (Re-send identical webhook)
echo "\n5. Testing Idempotency (Sending duplicate webhook)...\n";
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Host: localhost:8080'
]);
$dupResponse = curl_exec($ch);
$dupHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Duplicate Webhook Response (HTTP {$dupHttpCode}):\n";
echo "   " . $dupResponse . "\n";

$dupData = json_decode($dupResponse, true);
if ($dupHttpCode === 200 && isset($dupData['status']) && $dupData['status'] === 'already_paid') {
    echo "   ==> Idempotency Check: PASS (Successfully handled duplicate transaction)!\n";
} else {
    echo "   ==> Idempotency Check: FAILED!\n";
    exit(1);
}

echo "\n============================================\n";
echo "STORY 2.2 ALL WEBHOOK RECONCILIATION TESTS PASSED!\n";
echo "============================================\n";
