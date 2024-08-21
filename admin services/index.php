<style>
    
    .stats{
        display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
    }
    .stat-item{
        background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            width:200px;
    }
</style>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin services/index.php");
    exit;
}

include('includes/header.php');

?>
    <div class="stats">
    <?php

include('../php/connection.php');

// Fetch number of users
$sqlUsers = "SELECT COUNT(*) AS total_users FROM users";
$resultUsers = $conn->query($sqlUsers);
if ($resultUsers->num_rows > 0) {
    $rowUsers = $resultUsers->fetch_assoc();
    $totalUsers = $rowUsers['total_users'];
} else {
    $totalUsers = 0;
}

// Count total workers
$sqlWorkers = "SELECT COUNT(*) AS total_workers FROM mp_online_service_worker";
$resultWorkers = $conn->query($sqlWorkers);

if ($resultWorkers->num_rows > 0) {
    $rowWorkers = $resultWorkers->fetch_assoc();
    $totalWorkers = $rowWorkers['total_workers'];
} else {
    $totalWorkers = 0;
}


// Fetch number of services
$sqlServices = "SELECT COUNT(*) AS total_services FROM services";
$resultServices = $conn->query($sqlServices);
if ($resultServices->num_rows > 0) {
    $rowServices = $resultServices->fetch_assoc();
    $totalServices = $rowServices['total_services'];
} else {
    $totalServices = 0;
}

// Close connection
$conn->close();
?>
<div class="main-content">
    <div class="stats">
        <div class="stat-item">
            <h2>Total Users</h2>
            <p><?php echo $totalUsers; ?></p>
        </div>
        <div class="stat-item">
            <h2>Total Services</h2>
            <p><?php echo $totalServices; ?></p>
        </div>
        <div class="stat-item">
            <h2>Total Workers</h2>
            <p><?php echo $totalWorkers; ?></p>
        </div>
        <!-- Add more stats as needed -->
    </div>
</div>

    </div>
<?php include('includes/footer.php'); ?>
