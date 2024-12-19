<?php
// fetch_order_details.php
include './php/connection.php';

if (isset($_GET['orderid'])) {
    $orderid = $_GET['orderid'];

    $stmt = $conn->prepare("SELECT firstname, lastname, amount, status, orderid, pid, payer_email, currency, mobile, address, note, payment_date, deadline FROM payments WHERE orderid = ?");
    $stmt->bind_param("s", $orderid);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();

    echo json_encode($details);
    $stmt->close();
    $conn->close();
}
