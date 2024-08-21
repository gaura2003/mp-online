<?php
require('razorpay-php/Razorpay.php'); // Include Razorpay SDK

use Razorpay\Api\Api;

session_start();
include('./php/connection.php');

$apiKey = "your_razorpay_key_id";
$apiSecret = "your_razorpay_key_secret";
$api = new Api($apiKey, $apiSecret);

$service_id = $_POST['service_id'];
$user_id = $_SESSION['user_id'];

// Retrieve service details
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$service = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Generate Razorpay order
$orderData = [
    'receipt'         => 'rcptid_' . $service_id,
    'amount'          => $service['service_price'] * 100, // in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto-capture
];

$razorpayOrder = $api->order->create($orderData);
$orderId = $razorpayOrder['id'];

// Save order in your database (Optional)
// You can save the order ID, service ID, user ID, etc. for tracking the payment status

$_SESSION['razorpay_order_id'] = $orderId;

echo json_encode([
    "order_id" => $orderId,
    "service_name" => $service['service_name'],
    "amount" => $service['service_price'] * 100,
    "currency" => 'INR'
]);
?>
