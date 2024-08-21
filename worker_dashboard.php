<?php
session_start();
include('./php/connection.php');

// Check if the user is logged in and is a worker
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'worker') {
    $worker_id = $_SESSION['user_id'];

    // Prepare the SQL statement to fetch worker data
    $stmt = $conn->prepare("SELECT * FROM mp_online_service_worker WHERE id = ?");
    $stmt->bind_param("i", $worker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $worker = $result->fetch_assoc();
    } else {
        echo "No worker found with this ID.";
        exit;
    }

    $stmt->close();
} else {
    header("Location: index.php"); // Redirect to login if not logged in
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file link here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .dashboard-content {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .dashboard-item {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }
        .dashboard-item h3 {
            margin: 0;
            font-size: 24px;
        }
        .dashboard-item p {
            margin: 10px 0 0;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Welcome, <?php echo htmlspecialchars($worker['full_name']); ?></h2>
        </div>
        <div class="dashboard-content">
            <div class="dashboard-item">
                <h3>Balance</h3>
                <p>$<?php echo number_format($worker['balance'], 2); ?></p>
            </div>
            <div class="dashboard-item">
                <h3>Total Work Done</h3>
                <p><?php echo $worker['total_work_done']; ?></p>
            </div>
            <div class="dashboard-item">
                <h3>Pending Work</h3>
                <p><?php echo $worker['pending_work']; ?></p>
            </div>
            <div class="dashboard-item">
                <h3>Completed Work</h3>
                <p><?php echo $worker['completed_work']; ?></p>
            </div>
            <div class="dashboard-item">
                <h3>This Week's Earnings</h3>
                <p>$<?php echo number_format($worker['this_week_earning'], 2); ?></p>
            </div>
            <div class="dashboard-item">
                <h3>This Month's Earnings</h3>
                <p>$<?php echo number_format($worker['this_month_earning'], 2); ?></p>
            </div>
            <div class="dashboard-item">
                <h3>This Year's Earnings</h3>
                <p>$<?php echo number_format($worker['this_year_earning'], 2); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
