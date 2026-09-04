<?php
class QloVietQrWebhookModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_header = false;
    public $display_footer = false;

    public function initContent()
    {
        parent::initContent();
        $this->processWebhook();
    }

    public function processWebhook()
    {
        header('Content-Type: application/json');

        // 1. Authenticate webhook request.
        // The secret comes from the environment first so it never has to live in a
        // committed file (the DB seed is version-controlled; the .env is not).
        $expectedSecret = QloVietQr::getWebhookSecret();

        // Fail closed: with no secret configured, refuse rather than wave everyone
        // through. This endpoint marks bookings as paid.
        if ($expectedSecret === '') {
            header('Content-Type: application/json', true, 503);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Webhook secret is not configured. Set VIETQR_WEBHOOK_SECRET.'
            ));
            exit;
        }

        // Header is preferred (keeps the secret out of access logs and referrers),
        // but the query parameter stays supported: several Vietnamese payment
        // gateways only let you configure a plain callback URL.
        $tokenParam = (string) Tools::getValue('token');
        $headerSecret = isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? (string) $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '';
        if (!hash_equals($expectedSecret, $headerSecret) && !hash_equals($expectedSecret, $tokenParam)) {
            header('Content-Type: application/json', true, 401);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Unauthorized: Invalid webhook secret token'
            ));
            exit;
        }

        // 2. Read and decode JSON payload
        $rawInput = Tools::file_get_contents('php://input');
        $payload = json_decode($rawInput, true);

        if (!$payload && !empty($_POST)) {
            $payload = $_POST;
        }

        if (empty($payload)) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Bad Request: Empty or invalid JSON payload'
            ));
            exit;
        }

        // 3. Extract transfer content, reference, and amount
        // Supports VietQR/Casso/Bank format and PayOS format
        $content = '';
        $reference = '';
        $amount = 0;
        $transactionId = '';

        if (isset($payload['data']) && is_array($payload['data'])) {
            // PayOS format
            $content = isset($payload['data']['description']) ? $payload['data']['description'] : '';
            $reference = isset($payload['data']['reference']) ? $payload['data']['reference'] : '';
            $amount = isset($payload['data']['amount']) ? (float) $payload['data']['amount'] : 0;
            $transactionId = isset($payload['data']['transactionDateTime']) ? (string) $payload['data']['transactionDateTime'] : uniqid('TXN_');
        } else {
            // Standard VietQR / Gateway format
            $content = isset($payload['content']) ? $payload['content'] : (isset($payload['description']) ? $payload['description'] : '');
            $reference = isset($payload['reference']) ? $payload['reference'] : (isset($payload['order_reference']) ? $payload['order_reference'] : '');
            $amount = isset($payload['amount']) ? (float) $payload['amount'] : 0;
            $transactionId = isset($payload['transaction_id']) ? (string) $payload['transaction_id'] : uniqid('TXN_');
        }

        // Extract reference from transfer memo if not directly specified
        if (empty($reference) && !empty($content)) {
            if (preg_match('/HOTEL\s*([A-Z0-9_-]+)/i', $content, $matches)) {
                $reference = trim($matches[1]);
            } elseif (preg_match('/[A-Z]{9}/', $content, $matches)) {
                $reference = trim($matches[0]);
            }
        }

        if (empty($reference)) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Cannot extract order reference from payload or memo: ' . $content
            ));
            exit;
        }

        // 4. Locate Order
        $order = null;
        $ordersCollection = Order::getByReference($reference);
        if ($ordersCollection && $ordersCollection->count() > 0) {
            $order = $ordersCollection->getFirst();
        }

        if ((!$order || !Validate::isLoadedObject($order)) && is_numeric($reference)) {
            $order = new Order((int) $reference);
        }

        if (!$order || !Validate::isLoadedObject($order)) {
            header('Content-Type: application/json', true, 404);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Order not found for reference: ' . $reference
            ));
            exit;
        }

        // 5. Check Idempotency (Already Paid)
        $paidStateId = (int) Configuration::get('PS_OS_PAYMENT_ACCEPTED');
        if ((int) $order->getCurrentState() === $paidStateId) {
            header('Content-Type: application/json', true, 200);
            echo json_encode(array(
                'status' => 'already_paid',
                'message' => 'Order was already marked as paid.',
                'id_order' => (int) $order->id,
                'reference' => $order->reference
            ));
            exit;
        }

        // 6. Validate Amount (if provided)
        $orderTotal = (float) $order->total_paid;
        if ($amount > 0 && $amount < $orderTotal) {
            header('Content-Type: application/json', true, 422);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Paid amount is less than required order total.',
                'order_total' => $orderTotal,
                'amount_received' => $amount
            ));
            exit;
        }

        // 7. Record Payment & Transition Order State to Payment Accepted
        $order->addOrderPayment($amount > 0 ? $amount : $orderTotal, 'VietQR Bank Transfer', $transactionId);

        $history = new OrderHistory();
        $history->id_order = (int) $order->id;
        $history->changeIdOrderState($paidStateId, (int) $order->id);
        $history->addWithemail(true);

        header('Content-Type: application/json', true, 200);
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Order confirmed and payment accepted.',
            'id_order' => (int) $order->id,
            'reference' => $order->reference,
            'paid_amount' => $amount > 0 ? $amount : $orderTotal,
            'transaction_id' => $transactionId
        ));
        exit;
    }
}
