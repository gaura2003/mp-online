<?php
include('./php/connection.php');
session_start();

// Check if worker_id is provided in the URL
if (isset($_GET['user_id'])) {
    $worker_id = $_GET['user_id'];

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
    echo "No worker ID provided.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file link here -->
</head>
<body>
    <h2>Worker Profile</h2>
    <div class="worker-profile">
        <p><strong>ID:</strong> <?php echo $worker['id']; ?></p>
        <p><strong>Username:</strong> <?php echo $worker['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $worker['email']; ?></p>
        <p><strong>Full Name:</strong> <?php echo $worker['full_name']; ?></p>
        <p><strong>Phone Number:</strong> <?php echo $worker['phone_number']; ?></p>
        <p><strong>Address:</strong> <?php echo $worker['address']; ?></p>
        <p><strong>Created At:</strong> <?php echo $worker['created_at']; ?></p>
    </div>
</body>
</html>
