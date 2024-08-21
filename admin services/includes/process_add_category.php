<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include('connection.php');

$category_name = $_POST['category_name'];
$description = $_POST['description'];

$sql = "INSERT INTO service_categories (category_name, description) 
        VALUES ('$category_name', '$description')";

if ($conn->query($sql) === TRUE) {
    echo "New category added successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
