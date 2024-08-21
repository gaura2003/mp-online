<?php
session_start();
include('./php/connection.php');

// Ensure only admin can perform actions
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $worker_id = $_POST['id'];
    $action = $_POST['action'];

    // Perform the action based on the button clicked
    switch ($action) {
        case 'block':
            $sql = "UPDATE mp_online_service_workers SET status='inactive' WHERE id=?";
            break;
        case 'unblock':
            $sql = "UPDATE mp_online_service_workers SET status='active' WHERE id=?";
            break;
        case 'activate':
            $sql = "UPDATE mp_online_service_workers SET status='active' WHERE id=?";
            break;
        case 'deactivate':
            $sql = "UPDATE mp_online_service_workers SET status='inactive' WHERE id=?";
            break;
        default:
            header("Location: workers.php");
            exit;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $worker_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: manage_workers.php");
exit;
?>
