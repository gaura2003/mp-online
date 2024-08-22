<?php

define('DBNAME', 'mp_online_services');
define('DBUSER', 'root');
define('DBPASS', '');
define('DBHOST', 'localhost');

try {
    $db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Issue -> Connection failed: " . $e->getMessage();
}

// Handle sorting and searching for payments
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'payment_date';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// SQL query for payments with search and sorting
$sql = "SELECT * FROM services, payments
        WHERE services.id = payments.pid
        AND (firstname LIKE :search OR lastname LIKE :search OR payer_email LIKE :search OR orderid LIKE :search OR mobile LIKE :search)
        ORDER BY $sort_by $order";

$stmt = $db->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$payments = $stmt->fetchAll();

// User ID and name for accepting work
$user_id = $_SESSION['user_id'] ?? 123; // Replace with actual user ID
$user_name = $_SESSION['username'] ?? "John Doe"; // Replace with actual user name

// HTML starts here
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelance Work</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card {
            display: flex;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 400px;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            width: 100%;
            height: 150px;
            object-fit: contain;
        }
        .card-body {
            padding: 20px;
            color: #333;
        }
        .card-body p {
            margin: 10px 0;
        }
        .accept-btn {
            display: block;
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }
        .accept-btn:hover {
            background-color: #45a049;
        }
        .accepted {
            color: #555;
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
        }
        form{
            display:flex;
            justify-content: end;
    gap: 34px;
        }
    </style>
</head>
<body>

            <!-- Search Bar -->
            <form method="GET" action="" >
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
<div class="card-container">
    <?php
foreach ($payments as $row) {
    echo "<div class='card'>
            <div style='padding-left:20px;'>
<img src='{$row['image_url']}' alt='Service Image'>
             <p><strong>Service:</strong> {$row['service_name']}</p>
                <p><strong>Amount:</strong> {$row['amount']} INR</p>
            </div>
            <div class='card-body'>
                <p><strong >ID:</strong> {$row['orderid']}</p>
                <p><strong >Custmor Name:</strong><br> {$row['firstname']}&nbsp;{$row['lastname']}</p>
                <p><strong>Deadline:</strong> {$row['deadline']}</p>
                <p><strong>Published Date:</strong> {$row['payment_date']}</p>";

    // Only show accept button if the work is not already accepted
    if ($row['work_status'] == 'pending') {
        echo "<button class='accept-btn' onclick='acceptWork({$row['id']}, $user_id, \"$user_name\")'>Accept</button>";
    } else {
        echo "<div class='accepted'>Accepted by {$row['accepted_by']}</div>";
    }

    echo "</div></div>";
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
                    // Optionally, hide the card or reload the page to update the UI
                    location.reload();
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
