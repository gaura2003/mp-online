<?php 

define('DBNAME','mp_online_services');
define('DBUSER','root');
define('DBPASS','');
define('DBHOST','localhost');

try {
    $db = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
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

// Export functionality
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    // Set headers to download the file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payments.csv"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Write the header row
    fputcsv($output, ['Paid By', 'Payer Email', 'Order ID', 'Title', 'Paid Amount', 'Address', 'Mobile', 'Note', 'Order Date']);

    // Write data rows
    foreach ($payments as $row) {
        fputcsv($output, [
            $row['firstname'] . ' ' . $row['lastname'],
            $row['payer_email'],
            $row['orderid'],
            $row['service_name'],
            $row['amount'] . ' INR',
            $row['address'],
            $row['mobile'],
            $row['note'],
            $row['payment_date']
        ]);
    }

    // Close the output stream
    fclose($output);
    exit();
}

// User ID and name for accepting work
$user_id = $_SESSION['user_id'] ?? 123;  // Replace with actual user ID
$user_name = $_SESSION['username'] ?? "John Doe";  // Replace with actual user name

// HTML starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payments | Orders - Techno Smarter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .accept-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .accept-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 form-container">
            <h1>Orders</h1>
            <hr>

            <!-- Search Bar -->
            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search by name, email, order ID, mobile..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>

                <!-- Sorting Filter -->
                <div class="input-group mb-3">
                    <select class="form-select" name="sort_by">
                        <option value="payment_date" <?php echo $sort_by == 'payment_date' ? 'selected' : ''; ?>>Sort by Date</option>
                        <option value="amount" <?php echo $sort_by == 'amount' ? 'selected' : ''; ?>>Sort by Amount</option>
                        <option value="firstname" <?php echo $sort_by == 'firstname' ? 'selected' : ''; ?>>Sort by Name</option>
                    </select>
                    <select class="form-select" name="order">
                        <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                        <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                    </select>
                    <button class="btn btn-outline-secondary" type="submit">Sort</button>
                </div>
            </form>

          

            <table class="table">
                <thead>
                    <tr>
                        <th>Paid By</th>
                        <th>Payer Email</th>
                        <th>Order ID</th>
                        <th>Product Image</th>
                        <th>Title</th>
                        <th>Paid Amount</th>
                        <th>Address</th>
                        <th>Mobile</th>
                        <th>Note</th>
                        <th>Order Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($payments as $row) {
                       echo '<tr>
                       <td>'.$row['firstname'].' '.$row['lastname'].'</td>
                       <td>'.$row['payer_email'].'</td>
                       <td>'.$row['orderid'].'</td>
                      <td><img class="card-img-top" src="'.$row['image_url'].'" alt="Card image cap" height="30" style="width:auto;"></td>
                       <td>'.$row['service_name'].'</td>
                       <td>'.$row['amount'].' INR</td>
                       <td>'.$row['address'].'</td>
                       <td>'.$row['mobile'].'</td>
                       <td>'.$row['note'].'</td>
                       <td>'.$row['payment_date'].'</td>';
                       
                       // Only show accept button if the work is not already accepted
                       if ($row['work_status'] == 'pending') {
                           echo "<td><button class='accept-btn' onclick='acceptWork({$row['id']}, $user_id, \"$user_name\")'>Accept</button></td>";
                       } else {
                           echo "<td>Accepted by {$row['accepted_by']}</td>";
                       }
                       
                       echo "</tr>";
                    }
                    ?> 
                </tbody>
            </table> 
        </div>
    </div>
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
                    location.reload(); // Refresh the page to see the changes
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
