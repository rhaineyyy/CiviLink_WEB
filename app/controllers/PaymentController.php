<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

use App\Models\AppointmentsModel;

class PaymentController extends Controller
{
    private $secretKey;

    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('AppointmentsModel');
        $this->call->library('session');
        $this->call->library('io');

        // PayMongo Test Secret Key (sandbox)
        $this->secretKey = "sk_test_WVCR39tM4CH7vjysozPQrQYk";
    }

    // ==========================
    // LOAD PAYMENT PAGE
    // ==========================
    public function pay()
{
    if (!$this->session->has_userdata('resident_id')) {
        redirect('resident/login');
        return;
    }

    $residentId = $this->session->userdata('resident_id');

    // Fetch all unpaid completed appointments for the resident
    $appointments = $this->AppointmentsModel->getCompletedUnpaidByResident($residentId);

    $payView = APP_DIR . '/views/payment/pay.php';
    $noAppView = APP_DIR . '/views/payment/no_appointment.php';

    if (empty($appointments)) {
        if (file_exists($noAppView)) {
            $error = "No unpaid completed appointments found. Please complete an appointment before proceeding to payment.";
            require $noAppView;
        } else {
            echo "<p>⚠️ View not found at {$noAppView}</p>";
        }
    } else {
        if (file_exists($payView)) {
            require $payView; // pass $appointments array to the view
        } else {
            echo "<p>⚠️ View not found at {$payView}</p>";
        }
    }
}

    // ==========================
    // CREATE PAYMENT INTENT
    // ==========================
    public function createIntent()
    {
        header("Content-Type: application/json");

        $amount = $this->io->post('amount');
        $residentId = $this->session->userdata('resident_id');

        if (!$amount || !$residentId) {
            echo json_encode(["error" => "Missing amount or resident ID"]);
            exit;
        }

        $appointment = $this->AppointmentsModel->getLatestCompletedByResident($residentId);

        if (!$appointment) {
            echo json_encode(["error" => "No completed appointment found for this resident."]);
            exit;
        }

        $payload = [
            "data" => [
                "attributes" => [
                    "amount" => intval($amount * 100),
                    "payment_method_allowed" => ["gcash"],
                    "currency" => "PHP",
                    "description" => "Document Payment - Appointment #{$appointment['id']}",
                    "metadata" => [
                        "appointment_id" => $appointment['id']
                    ]
                ]
            ]
        ];

        $response = $this->paymongoRequest("/v1/payment_intents", $payload);
        echo $response;
        exit;
    }

    // ==========================
    // CREATE PAYMENT METHOD
    // ==========================
    public function createPaymentMethod()
    {
        header("Content-Type: application/json");

        $payload = [
            "data" => [
                "attributes" => [
                    "type" => "gcash"
                ]
            ]
        ];

        $response = $this->paymongoRequest("/v1/payment_methods", $payload);
        echo $response;
        exit;
    }

    // ==========================
    // ATTACH PAYMENT METHOD TO INTENT
    // ==========================
    public function attachMethod()
    {
        header("Content-Type: application/json");

        $intentId = $this->io->post('intent_id');
        $methodId = $this->io->post('method_id');

        if (!$intentId || !$methodId) {
            echo json_encode(["error" => "Missing parameters"]);
            exit;
        }

        $payload = [
            "data" => [
                "attributes" => [
                    "payment_method" => $methodId,
                    "return_url" => site_url('payment/success')
                ]
            ]
        ];

        $response = $this->paymongoRequest("/v1/payment_intents/$intentId/attach", $payload);
        echo $response;
        exit;
    }

    // ==========================
    // SUCCESS PAGE
    // ==========================
    public function success()
{
    $appointmentId = $this->io->get('appointment_id');
    if (!$appointmentId) {
        echo "<p>⚠️ Missing appointment ID.</p>";
        return;
    }

    $appointment = $this->AppointmentsModel->getById($appointmentId);

    if ($appointment) {
        // ✅ Update the payment status to 'paid'
        $this->AppointmentsModel->updatePaymentStatus($appointmentId, 'paid');
    }

    $successView = APP_DIR . '/views/payment/success.php';
    if (file_exists($successView)) {
        require $successView;
    } else {
        echo "<p>⚠️ View not found at {$successView}</p>";
    }
}

    // ==========================
    // FAILED PAGE
    // ==========================
    public function failed()
    {
        $failedView = APP_DIR . '/views/payment/failed.php';
        if (file_exists($failedView)) {
            require $failedView;
        } else {
            echo "<p>⚠️ View not found at {$failedView}</p>";
        }
    }

    // ==========================
    // PROCESS GCash PAYMENT
    // ==========================
    public function processGcash()
    {
        $appointmentId = $this->io->post('appointment_id');
        $amount = $this->io->post('amount');

        if (!$appointmentId || !$amount) {
            echo "<div class='error'>❌ Missing parameters.</div>";
            return;
        }

        $appointment = $this->AppointmentsModel->getById($appointmentId);

        if (!$appointment) {
            echo "<div class='error'>❌ Appointment not found.</div>";
            return;
        }

        // Step 1: Create payment intent
        $payload = [
            "data" => [
                "attributes" => [
                    "amount" => intval($amount * 100),
                    "currency" => "PHP",
                    "payment_method_allowed" => ["gcash"],
                    "description" => "Payment for Appointment #$appointmentId",
                    "metadata" => ["appointment_id" => $appointmentId]
                ]
            ]
        ];

        $intentResponse = json_decode($this->paymongoRequest("/v1/payment_intents", $payload), true);

        if (isset($intentResponse['errors'])) {
            echo "<div class='error'>❌ Failed to create payment intent: " . htmlspecialchars($intentResponse['errors'][0]['detail']) . "</div>";
            return;
        }

        $intentId = $intentResponse['data']['id'];

        // Step 2: Create payment method
        $methodPayload = [
            "data" => [
                "attributes" => [
                    "type" => "gcash"
                ]
            ]
        ];

        $methodResponse = json_decode($this->paymongoRequest("/v1/payment_methods", $methodPayload), true);

        if (isset($methodResponse['errors'])) {
            echo "<div class='error'>❌ Failed to create payment method: " . htmlspecialchars($methodResponse['errors'][0]['detail']) . "</div>";
            return;
        }

        $methodId = $methodResponse['data']['id'];

        // Step 3: Attach method to intent
        $attachPayload = [
            "data" => [
                "attributes" => [
                    "payment_method" => $methodId,
                    "return_url" => site_url('payment/success?appointment_id=' . $appointmentId)
                ]
            ]
        ];

        $attachResponse = json_decode($this->paymongoRequest("/v1/payment_intents/$intentId/attach", $attachPayload), true);

        $checkoutUrl = $attachResponse['data']['attributes']['next_action']['redirect']['url'] ?? null;

        if ($checkoutUrl) {
            header("Location: $checkoutUrl");
            exit;
        } else {
            echo "<div class='error'>❌ Checkout URL missing. Full response: <pre>" . print_r($attachResponse, true) . "</pre></div>";
        }
    }

    // ==========================
    // PAYMONGO CURL REQUEST
    // ==========================
    private function paymongoRequest($endpoint, $payload)
    {
        $url = "https://api.paymongo.com" . $endpoint;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic " . base64_encode($this->secretKey . ":"),
                "Content-Type: application/json"
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            curl_close($curl);
            return json_encode(["error" => curl_error($curl)]);
        }

        curl_close($curl);
        return $response;
    }
}