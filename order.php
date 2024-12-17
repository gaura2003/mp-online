<?php
// Start session if needed
session_start();

// Include database connection
include './php/connection.php';

// Example: Assume user's email is stored in the session
$user_email = $_SESSION['email'];

// Prepare the SQL query to fetch order history
$stmt = $conn->prepare("SELECT orderid, amount, status, payment_date, deadline FROM payments WHERE payer_email = ?");
$stmt->bind_param("s", $user_email);

// Execute the statement
$stmt->execute();

// Fetch the results
$result = $stmt->get_result();
// Check if there are any records
if ($result->num_rows > 0) {
    echo "<h2>Your Order History</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Order ID</th><th>Amount</th><th>Status</th><th>Payment Date</th><th>Deadline</th></tr>";

    // Loop through each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['orderid']) . "</td>";
        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['payment_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['deadline']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No order history found.";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
