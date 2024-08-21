<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include('../php/connection.php');

$user_id = $_POST['user_id'];
$service_id = $_POST['service_id'];
$status = $_POST['status'];
$comments = $_POST['comments'];

$sql = "INSERT INTO service_requests (user_id, service_id, status, comments) 
        VALUES ('$user_id', '$service_id', '$status', '$comments')";

if ($conn->query($sql) === TRUE) {
    echo "New service request added successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
