<?php
// Start session if needed
session_start();

// Include database connection
include './php/connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id']; // Sanitize session data

// Get the status from the query parameter (pending, success)
$status = isset($_GET['status']) ? $_GET['status'] : 'pending'; // Default to 'pending'

// Fetch counts for pending and completed work
$countQuery = "
    SELECT 
        SUM(CASE WHEN work_status = 'progress' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN work_status = 'success' THEN 1 ELSE 0 END) AS completed_count
    FROM payments 
    WHERE accepted_by_id = ?
";
$countStmt = $conn->prepare($countQuery);

if (!$countStmt) {
    error_log("Database Error: " . $conn->error);
    die("An error occurred. Please try again later.");
}

$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$countResult = $countStmt->get_result();
$counts = $countResult->fetch_assoc();

$pendingCount = $counts['pending_count'] ?? 0;
$completedCount = $counts['completed_count'] ?? 0;

// Update worker's pending and completed work counts in the database
$updateWorkerQuery = "
    UPDATE mp_online_service_worker 
    SET pending_work = ?, completed_work = ?
    WHERE id = ?
";
$updateStmt = $conn->prepare($updateWorkerQuery);

if (!$updateStmt) {
    error_log("Database Error: " . $conn->error);
    die("An error occurred while updating worker data.");
}

$updateStmt->bind_param("iii", $pendingCount, $completedCount, $user_id);
$updateStmt->execute();

// Display updated counts for debugging purposes (optional)
echo "<h2>Pending Work: $pendingCount | Completed Work: $completedCount</h2>";

// Close the update statement
$updateStmt->close();

echo "<h2 class='padding'>" . ucfirst($status) . " Work History</h2>";
echo '<div class="box">';

// Prepare the SQL query to fetch order history where work_status is 'progress'
if ($status === 'success') {
    $sql = "SELECT orderid, pid, amount, work_status, payment_date, deadline FROM payments WHERE accepted_by_id = ? AND work_status = 'success'";
} else {
    // Fetch records where work_status is 'pending' or 'progress'
    $sql = "SELECT orderid, pid, amount, work_status, payment_date, deadline FROM payments WHERE accepted_by_id = ? AND (work_status = ? OR work_status = 'progress')";
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Database Error: " . $conn->error);
    die("An error occurred. Please try again later.");
}

if ($status === 'success') {
    $stmt->bind_param("i", $user_id); // Only bind user_id for success status
} else {
    $stmt->bind_param("is", $user_id, $status); // Bind both user_id and status for pending or progress
}

$stmt->execute();
$result = $stmt->get_result();

// Check if there are any records
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fetch product details using pid
        $pid = (int)$row['pid']; // Cast to integer for safety
        $productStmt = $conn->prepare("SELECT service_name, image_url FROM services WHERE id = ?");
        
        if (!$productStmt) {
            error_log("Database Error: " . $conn->error);
            continue;
        }

        $productStmt->bind_param("i", $pid);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $product = $productResult->fetch_assoc();
        
        $orderId = htmlspecialchars($row['orderid'], ENT_QUOTES, 'UTF-8');
        $amount = htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8');
        $workStatus = htmlspecialchars($row['work_status'], ENT_QUOTES, 'UTF-8');
        $deadline = htmlspecialchars($row['deadline'], ENT_QUOTES, 'UTF-8');

        // Replace 'progress' with 'pending' for display purposes
        if ($workStatus === 'progress') {
            $workStatus = 'pending';
        } elseif ($workStatus === 'success') {
            $workStatus = 'completed';
        } else {
            $workStatus = 'pending';
        }
    
?>
       <div class="order-card">
    <?php if ($product): ?>
        <div class="service-details">
            <img src="<?php echo htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" 
                 alt="<?php echo htmlspecialchars($product['service_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                 class="service-image">
            <h4>Service Name: <?php echo htmlspecialchars($product['service_name'], ENT_QUOTES, 'UTF-8'); ?></h4>
        </div>
    <?php endif; ?>
    <div class="order-details">
        <span>Order ID: </span>
        <div><b>#<?php echo $orderId; ?></b></div>
        <span>Amount:</span>
        <div>$<?php echo $amount; ?></div>

        <span>Status:</span>
        <div class="status status-<?php echo strtolower($workStatus); ?>">
            <?php echo $workStatus; ?>
        </div>
        <span>Deadline:</span>
        <div><?php echo $deadline; ?></div>
    </div>

    <?php if ($workStatus !== 'completed'): // Only show action buttons if work status is not completed ?>
        <div class="action-buttons">
            <button class="view-details" onclick="viewDetails('<?php echo $orderId; ?>')">View Details</button>
            <button class="send-document" onclick="sendDocument('<?php echo $orderId; ?>')">Send Work</button>
        </div>
    <?php endif; ?>
</div>

<?php
        $productStmt->close();
    }
} else {
    echo "No " . $status . " orders found.";
}
echo "</div>";

// Close statement and connection
$stmt->close();
$conn->close();
?>
<style>
  /* History  */
.box {
  gap: 15px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 20px;
}

.order-card {
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 15px 20px;
  margin: 10px;
  width: 100%;
  max-width: 900px;
  box-sizing: border-box;
  display: flex;
  justify-content: space-between;
  align-items: center;
  /* Ensure content is vertically aligned */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.order-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.service-details h4 {
  font-size: 1rem;
  /* Smaller font size */
  margin-bottom: 8px;
  /* Less margin */
  color: #444;
  font-weight: 500;
}

.service-image {
  height: 80px;
  /* Set fixed height */
  border-radius: 8px;
  border: 2px solid #f0f0f0;
  transition: transform 0.3s ease;
}

.service-image:hover {
  transform: scale(1.05);
}

.order-details {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 8px;
  /* Reduced gap */
}

.order-details div {
  font-size: 0.9rem;
  /* Smaller font for details */
}

.status {
  border-radius: 5px;
  font-weight: bold;
  display: inline-block;
  padding: 5px 10px;
  /* Compact padding for better alignment */
  text-transform: capitalize;
  /* Ensure the first letter is capitalized */
  font-size: 0.9rem;
  /* Slightly smaller font size */
  color: white;
  /* White text for contrast */
  text-align: center;
  min-width: 80px;
  /* Consistent width for all statuses */
}

/* Status-specific colors */
.status-pending {
  background-color: #0dcaf0;
  /* Lighter blue for pending */
}

.status-success {
  background-color: #198754;
  /* Green for success */
}

.status-progress {
  background-color: #f0ad4e;
  /* Orange for progress */
}

.status-failed {
  background-color: #d9534f;
  /* Red for failure */
}

/* Hover effect (optional) */
.status:hover {
  opacity: 0.9;
  /* Slight dimming effect */
  transition: opacity 0.3s ease;
}


.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 8px;
  /* Reduced gap */
  margin-top: 10px;
  /* Reduced margin */
}

.view-details,
.send-document {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 8px 15px;
  /* Smaller padding */
  border-radius: 5px;
  cursor: pointer;
  font-size: 0.9rem;
  /* Smaller font size */
  transition: background-color 0.3s ease;
}

.view-details:hover,
.send-document:hover {
  background-color: #0056b3;
}

/* Mobile-responsive styles */
@media (max-width: 768px) {
  .order-card {
    flex-direction: column;
    align-items: flex-start;
    /* Align content to the left */
    padding: 10px 15px;
  }

  .service-image {
    height: 60px;
  }

  .view-details,
  .send-document {
    padding: 6px 12px;
    font-size: 0.8rem;
  }
}

/* Extra small screen styles */
@media (max-width: 480px) {
  .order-card {
    padding: 8px 12px;
  }

  .order-details {
    grid-template-columns: 1fr;
  }

  .status {
    font-size: 0.8rem;
    padding: 4px 6px;
  }

  .view-details,
  .send-document {
    font-size: 0.8rem;
    padding: 5px 10px;
  }
}

/* Small screen styles */
@media (min-width: 481px) and (max-width: 768px) {
  .order-card {
    padding: 10px 15px;
  }

  .service-image {
    height: 70px;
  }

  .view-details,
  .send-document {
    font-size: 0.9rem;
  }

  .status {
    font-size: 0.9rem;
    padding: 6px 10px;
  }

  .action-buttons {
    gap: 6px;
  }

  .view-details,
  .send-document {
    padding: 7px 13px;
  }

  .order-details div {
    font-size: 0.8rem;
  }

  .service-details h4 {
    font-size: 0.9rem;
  }

  .order-details {
    gap: 6px;
  }
}

/* Medium screen styles */
@media (min-width: 769px) and (max-width: 1024px) {
  .order-card {
    padding: 12px 18px;
  }

  .service-image {
    height: 80px;
  }

  .view-details,
  .send-document {
    font-size: 1rem;
    padding: 8px 15px;
  }

  .status {
    font-size: 1rem;
    padding: 8px 12px;
  }

  .action-buttons {
    gap: 8px;

  }
}
</style>