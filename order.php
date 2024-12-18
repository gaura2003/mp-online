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
echo "<h2 class='padding'>Your Order History</h2>";

echo '<div class="box">';
// Check if there are any records
if ($result->num_rows > 0) {
    // Loop through each row and display it in the card layout
    while ($row = $result->fetch_assoc()) {
        ?>
        <div class="order-card">
            <h3>Order ID: <?php echo htmlspecialchars($row['orderid']); ?></h3>
            <div class="order-details">
                <span>Amount:</span>
                <div>$<?php echo htmlspecialchars($row['amount']); ?></div>

                <span>Status:</span>
                <div class="status <?php echo strtolower(htmlspecialchars($row['status'])); ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                </div>

                <span>Payment Date:</span>
                <div><?php echo htmlspecialchars($row['payment_date']); ?></div>

                <span>Deadline:</span>
                <div><?php echo htmlspecialchars($row['deadline']); ?></div>
            </div>
        </div>
        
        <?php
    }
} else {
    echo "No order history found.";
}
echo "</div>";
// Close statement and connection
$stmt->close();
$conn->close();
?>

<!-- Add the required CSS style in the <head> section -->
<style>
    
    .box{
        display: grid;
        grid-template-columns: auto auto;
    }
    .order-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin: 10px;
        width: 500px;
        box-sizing: border-box;
    }
    .order-card h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #333;
    }
    .order-details {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 10px;
        margin-bottom: 20px;
    }
    .order-details span {
        font-weight: bold;
        color: #555;
    }
    .order-details div {
        color: #333;
        word-wrap: break-word;
    }
    .status {
        padding: 8px 15px;
        background-color: #e3e3e3;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
        color: #333;
    }
    .status.completed {
        background-color: #28a745;
        color: white;
    }
    .status.pending {
        background-color: #ffc107;
        color: white;
    }
    .status.failed {
        background-color: #dc3545;
        color: white;
    }
</style>
