<?php

// Correct path to connection.php
include __DIR__ . '/../php/connection.php';

// Ensure database connection is active
if (!$conn || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Sanitize and handle search and sorting inputs
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort_by = isset($_GET['sort_by']) ? $conn->real_escape_string($_GET['sort_by']) : 'payment_date';
$order = (isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC') ? 'ASC' : 'DESC';

// SQL query for payments with search and sorting, filtering out accepted work
$sql = "SELECT * FROM services, payments
        WHERE services.id = payments.pid
        AND (firstname LIKE ? OR lastname LIKE ? OR payer_email LIKE ? OR orderid LIKE ? OR mobile LIKE ?)
        AND (accepted_by IS NULL OR accepted_by = '')
        ORDER BY $sort_by $order";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL Error: " . $conn->error);
}

// Prepare the search parameter
$search_param = "%$search%";

// Bind parameters and execute
$stmt->bind_param('sssss', $search_param, $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$payments = $result->fetch_all(MYSQLI_ASSOC);

// User ID and name for accepting work
$user_id = $_SESSION['user_id'] ?? 123; // Replace with actual user ID
$user_name = $_SESSION['username'] ?? "John Doe"; // Replace with actual user name

// Close the statement
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelance Work</title>

</head>

<body>
    <!-- Search Bar -->
    <form method="GET" action="" class="d-flex justify-content-end gap-4">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search by name, email, order ID, mobile..." name="search" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>

        <!-- Sorting Filter -->
        <div class="input-group mb-3">
            <select class="form-select" name="sort_by">
                <option value="payment_date" <?php echo $sort_by == 'payment_date' ? 'selected' : ''; ?>>Sort by Date</option>
                <option value="amount" <?php echo $sort_by == 'amount' ? 'selected' : ''; ?>>Sort by Amount</option>
                <option value="service_name" <?php echo $sort_by == 'service_name' ? 'selected' : ''; ?>>Sort by Name</option>
            </select>
            <select class="form-select" name="order">
                <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
            </select>
            <button class="btn btn-outline-secondary" type="submit">Sort</button>
        </div>
    </form>

    <h2>Available Work</h2>
    <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
        <?php
        foreach ($payments as $row) {
            
            echo '<div class="d-flex flex-column bg-white rounded-3 shadow-sm" style="width: 400px; height:300px; transition: transform 0.3s ease;"   data-bs-toggle="hover"> ';
            echo "<div class='d-flex'>
            <div style='padding-left:20px;'>
            <img src='{$row['image_url']}' alt='Service Image'  class='w-100' style='height: 150px; object-fit: contain;'>
             <p><strong>Service:</strong> {$row['service_name']}</p>
                <p><strong>Amount:</strong> {$row['amount']} INR</p>
            </div>
            <div class='p-3' style='color:#333;'>
                <p><strong >ID:</strong> {$row['orderid']}</p>
                <p><strong >Custmor Name:</strong><br> {$row['firstname']}&nbsp;{$row['lastname']}</p>
                <p><strong>Deadline:</strong> {$row['deadline']}</p>
                <p><strong>Published Date:</strong> {$row['payment_date']}</p>
            </div> 
            </div>";
             // Only show accept button if the work is not already accepted
    if ($row['work_status'] == 'pending') {
        echo "<button class='btn w-50 mx-auto mb-3 font-family-sans-serif fw-bold fs-4'  style='background-color: #0c5df4; color:white; transition: background-color 0.3s ease;' onclick='acceptWork({$row['id']}, $user_id, \"$user_name\")'>Accept</button>";
    } else {
        echo "<div class='text-center mb-3' style='color: #555; font-weight: bold;' >Accepted by {$row['accepted_by']}</div>";
    }
            echo '</div>';
        }
        ?>
    </div>


    <script>
        function acceptWork(paymentId, userId, userName) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "includes/accept_work.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        alert('Work accepted successfully!');
                        location.reload(); // Optionally reload to update the UI
                    } else {
                        alert('Failed to accept work. Please try again.');
                    }
                }
            };
            xhr.send("payment_id=" + paymentId + "&user_id=" + userId + "&user_name=" + userName);
        }
    </script>
</body>

</html>