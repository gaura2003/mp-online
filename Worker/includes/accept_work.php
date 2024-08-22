<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $mysqli = new mysqli("localhost", "root", "", "mp_online_services");

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Retrieve and sanitize POST data
    $payment_id = intval($_POST['payment_id']);
    $user_id = $_SESSION['user_id'];

    // Check if worker_id is provided and retrieve worker details
    if (isset($user_id)) {
        // Prepare the SQL statement to fetch worker data
        $stmt = $mysqli->prepare("SELECT * FROM mp_online_service_worker WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the worker's data
            $worker = $result->fetch_assoc();
            $worker_name = $mysqli->real_escape_string($worker['username']);

            // Update the payments table with the worker's username and set work_status to 'progress'
            $sql = "UPDATE payments SET work_status = 'progress', accepted_by = '$worker_name' WHERE id = $payment_id";

            if ($mysqli->query($sql) === TRUE) {
                echo "Work accepted successfully by $worker_name";
            } else {
                echo "Error updating payment: " . $mysqli->error;
            }
        } else {
            echo "No worker found with this ID.";
        }

        $stmt->close();
    } else {
        echo "No worker ID provided.";
    }

    // Close the connection
    $mysqli->close();
}
?>
