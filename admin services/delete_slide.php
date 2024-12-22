<?php
// Include database connection
include '../php/connection.php';

$id = $_GET['id'];

// Delete the slide
$sql = "DELETE FROM slides WHERE id = $id";
if ($conn->query($sql) === TRUE) {
    header("Location: ../admin service/adminSlider.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
