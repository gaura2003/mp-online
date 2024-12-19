<?php
// Start session if needed
session_start();

// Include database connection
include './php/connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details to get the email
$sql = "SELECT email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

$user_email = $user['email'];

// Prepare the SQL query to fetch order history using the user's email
$stmt = $conn->prepare("SELECT orderid, pid, amount, work_status, payment_date, deadline FROM payments WHERE payer_email = ?");
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
        // Fetch product details using pid
        $pid = $row['pid'];
        $productStmt = $conn->prepare("SELECT service_name, image_url FROM services WHERE id = ?");
        $productStmt->bind_param("i", $pid);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $product = $productResult->fetch_assoc();
?>
        <div class="order-card">
            <?php if ($product): ?>
                <div class="service-details">
                    <img src="./admin services/<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['service_name']); ?>" class="service-image">
                    <h4>Service Name:</h4>
                    <h4><?php echo htmlspecialchars($product['service_name']); ?></h4>
                </div>
            <?php endif; ?>
            <div class="order-details">
                <span>Order ID: </span>
                <div><b>#<?php echo htmlspecialchars($row['orderid']); ?></b></div>
                <span>Amount:</span>
                <div>$<?php echo htmlspecialchars($row['amount']); ?></div>

                <span>Status:</span>
                <div class="status status-<?php echo strtolower(htmlspecialchars($row['work_status'])); ?>">
                    <?php echo htmlspecialchars($row['work_status']); ?>
                </div>

                <span>Payment Date:</span>
                <div><?php echo htmlspecialchars($row['payment_date']); ?></div>

                <span>Deadline:</span>
                <div><?php echo htmlspecialchars($row['deadline']); ?></div>
            </div>

            <div class="action-buttons">
                <button class="view-details" onclick="viewDetails('<?php echo $row['orderid']; ?>')">View Details</button>
                <button class="send-document" onclick="sendDocument('<?php echo $row['orderid']; ?>')">Send Document</button>
            </div>
        </div>
<?php
        $productStmt->close();
    }
} else {
    echo "No order history found.";
}
echo "</div>";

// Close statement and connection
$stmt->close();
$conn->close();
?>


<style>
    .box {
        gap: 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .order-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin: 10px;
        width: 865px;
        box-sizing: border-box;
        display: flex;
        gap: 30px;
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

    .service-details {
        margin-top: 20px;
    }

    .service-details h4 {
        font-size: 1.2rem;
        color: #333;
    }

    .service-image {
        max-width: 100%;
        height: auto;
        margin-top: 10px;
        border-radius: 10px;
    }

    .status {
        border-radius: 5px;
        font-weight: bold;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .status-progress {
        background-color: #f0ad4e;
        /* Orange for progress */
        color: white;
        /* Text color for better contrast */
    }

    .status-completed {
        background-color: #198754;
        /* Darker green for completion */
        color: white;
    }

    .status-pending {
        background-color: #0dcaf0;
        /* Lighter blue for pending */
        color: white;
    }

    .status-failed {
        background-color: #d9534f;
        /* Rich red for failure */
        color: white;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 20px;
    }


    .view-details,
    .send-document {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
    }

    .view-details:hover,
    .send-document:hover {
        background-color: #0056b3;
    }


    /* Mobile-responsive styles */
    @media (max-width: 768px) {
        .order-card {
            width: 100%;
            padding: 15px;
            flex-direction: column;
            gap: 15px;
        }

        .order-card h3 {
            font-size: 1.2rem;
        }

        .view-details,
        .send-document {
            font-size: 0.9rem;
            padding: 8px 16px;
        }
    }

    /* Extra small screen styles */
    @media (max-width: 480px) {
        .order-card {
            padding: 10px;
        }

        .status {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        .view-details,
        .send-document {
            font-size: 0.8rem;
            padding: 6px 12px;
        }
    }
</style>
<script>
    function viewDetails(orderId) {
        fetch(`fetch_order_details.php?orderid=${orderId}`)
            .then(response => response.json())
            .then(data => {
                const details = `
                <p><strong>First Name:</strong> ${data.firstname}</p>
                <p><strong>Last Name:</strong> ${data.lastname}</p>
                <p><strong>Amount:</strong> $${data.amount}</p>
                <p><strong>Status:</strong> ${data.status}</p>
                <p><strong>Order ID:</strong> ${data.orderid}</p>
                <p><strong>Product ID:</strong> ${data.pid}</p>
                <p><strong>Email:</strong> ${data.payer_email}</p>
                <p><strong>Currency:</strong> ${data.currency}</p>
                <p><strong>Mobile:</strong> ${data.mobile}</p>
                <p><strong>Address:</strong> ${data.address}</p>
                <p><strong>Note:</strong> ${data.note}</p>
                <p><strong>Payment Date:</strong> ${data.payment_date}</p>
                <p><strong>Deadline:</strong> ${data.deadline}</p>
            `;
                document.getElementById('order-details').innerHTML = details;
                document.getElementById('details-modal').classList.remove('hidden');
            });
    }

    function sendDocument(orderId) {
        document.getElementById('document-modal').classList.remove('hidden');
    }

    function sendDocuments() {
        const files = document.getElementById('document-input').files;
        if (files.length === 0) {
            alert('Please select at least one document.');
            return;
        }

        const formData = new FormData();
        for (const file of files) {
            formData.append('documents[]', file);
        }

        // WhatsApp API integration (placeholder logic)
        const mobileNumber = "91XXXXXXXXXX"; // Replace with dynamic mobile number
        const message = `Documents for your order have been sent.`;
        const whatsappURL = `https://api.whatsapp.com/send?phone=${mobileNumber}&text=${encodeURIComponent(message)}`;

        window.open(whatsappURL, '_blank');
    }

    function closeModal() {
        document.querySelectorAll('.modal').forEach(modal => modal.classList.add('hidden'));
    }
</script>