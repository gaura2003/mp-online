<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
<?php
session_start();
require('razorpay-php/Razorpay.php');
include('./php/connection.php');

if (!isset($_SESSION['pid'])) {
    header("Location: index.php");
    exit;
}

$pid = $_SESSION['pid'];

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;
$error = "Payment Failed";

$keyId = 'rzp_test_E5BNM56ZxxZAwk'; 
$keySecret = 'uXo5UAsgnT7zglLrmsH749Je';

if (!empty($_POST['razorpay_payment_id'])) {
    $api = new Api($keyId, $keySecret);

    try {
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        // Verify payment signature
        $api->utility->verifyPaymentSignature($attributes);
    } catch (SignatureVerificationError $e) {
        $success = false;
        $error = 'Razorpay Error: ' . $e->getMessage();
    }
}
if ($success === true) {
    // Retrieve deadline from session
    $deadline = $_SESSION['deadline'];

    // Other session and payment details
    $firstname = $_SESSION['fname'];
    $lastname = $_SESSION['lname'];
    $email = $_SESSION['email'];
    $mobile = $_SESSION['mobile'];
    $address = $_SESSION['address'];
    $note = $_SESSION['note'];
    $productinfo = 'Payment';

    $orderid = $_SESSION['razorpay_order_id']; // Razorpay Order ID
    $amount = $_SESSION['price'];
    $status = 'success';
    $currency = 'INR';
    $date = new DateTime(null, new DateTimezone("Asia/Kolkata"));
    $payment_date = $date->format('Y-m-d H:i:s');

    // Get the last transaction ID and increment it
    $stmt = $conn->prepare("SELECT MAX(txnid) FROM payments");
    $stmt->execute();
    $stmt->bind_result($last_txnid);
    $stmt->fetch();
    $stmt->close();
    echo '<div>';
    if (!isset($error_found)) {
        $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $result = $stmt->get_result();
        $service = $result->fetch_assoc();
        $stmt->close();
        echo '<div class="card" style="width: 18rem;">';
        echo '<img class="card-img-top" src="./admin services/' . $service['image_url'] . '" alt="Service Image">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $service['service_name'] . '</h5>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    // Generate new transaction ID
    $new_txnid = $last_txnid ? $last_txnid + 1 : 1; // Start from 1 if no records exist

    // Check if transaction already exists
    $stmt = $conn->prepare("SELECT count(*) FROM payments WHERE txnid = ?");
    $stmt->bind_param("i", $new_txnid);
    $stmt->execute();
    $stmt->bind_result($countts);
    $stmt->fetch();
    $stmt->close();

    if ($countts <= 0) {
        // Only insert if txnid is unique
        $stmt = $conn->prepare("INSERT INTO payments (firstname, lastname, amount, status, txnid, orderid, pid, payer_email, currency, mobile, address, note, payment_date, deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsisssssssss", $firstname, $lastname, $amount, $status, $new_txnid, $orderid, $pid, $email, $currency, $mobile, $address, $note, $payment_date, $deadline);

        if ($stmt->execute()) {
            echo '<h2 style="color:#33FF00;">Your payment has been successful.</h2><hr>';
            // Display transaction details
            echo '<table class="table">';
            echo '<tr><th>Transaction ID:</th><td>' . $new_txnid . '</td></tr>';
            echo '<tr><th>Order ID:</th><td>' . $orderid . '</td></tr>';
            echo '<tr><th>Paid Amount:</th><td>' . $amount . ' ' . $currency . '</td></tr>';
            echo '<tr><th>Payment Status:</th><td>' . $status . '</td></tr>';
            echo '<tr><th>Payer Email:</th><td>' . $email . '</td></tr>';
            echo '<tr><th>Name:</th><td>' . $firstname . ' ' . $lastname . '</td></tr>';
            echo '<tr><th>Address:</th><td>' . $address . '</td></tr>';
            echo '<tr><th>Note:</th><td>' . $note . '</td></tr>';
            echo '<tr><th>Date:</th><td>' . $payment_date . '</td></tr>';
            echo '<tr><th>Deadline:</th><td>' . $deadline . '</td></tr>';
            echo '</table>';
        } else {
            echo '<p>Error saving payment details to the database.</p>';
        }
        $stmt->close();
    } else {
        echo '<h2 style="color:red;">Transaction ID already exists.</h2>';
    }
} else {
    echo "<p><div class='errmsg'>Invalid Transaction. Please Try Again</div></p><p>{$error}</p>";
}
?>