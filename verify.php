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
    // Retrieve payment details from session
    $firstname = $_SESSION['fname'];
    $lastname = $_SESSION['lname'];
    $email = $_SESSION['email'];
    $mobile = $_SESSION['mobile'];
    $address = $_SESSION['address'];
    $note = $_SESSION['note'];
    $productinfo = 'Payment';
    $orderid = $_SESSION['razorpay_order_id'];
    $amount = $_SESSION['price'];
    $status = 'success';
    $currency = 'INR';
    $date = new DateTime(null, new DateTimezone("Asia/Kolkata"));
    $payment_date = $date->format('Y-m-d H:i:s');
    $deadline = $_SESSION['deadline'];

    // Get the last transaction ID and increment it
    $stmt = $conn->prepare("SELECT MAX(txnid) FROM payments");
    $stmt->execute();
    $stmt->bind_result($last_txnid);
    $stmt->fetch();
    $stmt->close();

    // Generate new transaction ID
    $new_txnid = $last_txnid ? $last_txnid + 12 : 1; // Start from 1 if no records exist

    // Insert payment details into database
    $stmt = $conn->prepare("INSERT INTO payments (firstname, lastname, amount, status, txnid, orderid, pid, payer_email, currency, mobile, address, note, payment_date, deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsisssssssss", $firstname, $lastname, $amount, $status, $new_txnid, $orderid, $pid, $email, $currency, $mobile, $address, $note, $payment_date, $deadline);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Payment Status</title>
</head>
<body>
    <?php if ($success === true): ?>
        <!-- Payment Success Modal -->
        <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-labelledby="paymentSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentSuccessModalLabel">Payment Successful</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold">Thank you for your payment!</p>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Transaction ID:</strong> <?php echo $new_txnid; ?></li>
                            <li class="list-group-item"><strong>Order ID:</strong> <?php echo $orderid; ?></li>
                            <li class="list-group-item"><strong>Amount Paid:</strong> <?php echo $amount . ' ' . $currency; ?></li>
                            <li class="list-group-item"><strong>Payment Status:</strong> <?php echo $status; ?></li>
                            <li class="list-group-item"><strong>Payer Email:</strong> <?php echo $email; ?></li>
                            <li class="list-group-item"><strong>Name:</strong> <?php echo $firstname . ' ' . $lastname; ?></li>
                            <li class="list-group-item"><strong>Mobile:</strong> <?php echo $mobile; ?></li>
                            <li class="list-group-item"><strong>Address:</strong> <?php echo $address; ?></li>
                            <li class="list-group-item"><strong>Note:</strong> <?php echo $note; ?></li>
                            <li class="list-group-item"><strong>Payment Date:</strong> <?php echo $payment_date; ?></li>
                            <li class="list-group-item"><strong>Deadline:</strong> <?php echo $deadline; ?></li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="redirectToHistory">Go to Order History</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Show the modal
            var paymentModal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
            paymentModal.show();

            // Redirect on button click
            document.getElementById('redirectToHistory').addEventListener('click', function () {
                window.location.href = 'order.php';
            });

            // Auto-redirect after 5 seconds
            setTimeout(function () {
                window.location.href = 'order.php';
            }, 5000);
        </script>
    <?php else: ?>
        <p class="text-danger">Invalid Transaction. Please try again.</p>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>
