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
  
</head>
<body>
    <a href="profile.php?user_id=<?php $worker_id ?>">profile</a>
    <?php
    include('./includes/work.php');
    ?>

</body>
</html>
