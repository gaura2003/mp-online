<?php 
session_start();
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

// Handle sorting and searching
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'payment_date';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// SQL query with search and sorting
$sql = "SELECT * FROM services, payments 
        WHERE services.id = payments.pid 
        AND (firstname LIKE :search OR lastname LIKE :search OR payer_email LIKE :search OR orderid LIKE :search OR mobile LIKE :search)
        ORDER BY $sort_by $order";

$stmt = $db->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$rows = $stmt->fetchAll();

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
    foreach ($rows as $row) {
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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payments | Orders - Techno Smarter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 form-container">
            <h1>Payments | Orders</h1>
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

            <!-- Download Button -->
            <div class="mb-3">
                <a href="?export=csv" class="btn btn-primary">Download CSV</a>
            </div>

            <table class="table">
                <tr>
                     <th>Paid By </th>
                     <th>Accepted by</th>
                     <th>Paid to Worker</th>
                     <th>Order Id </th>
                     <th>Image</th>
                     <th>Service Name</th>
                     <th>Paid Amount</th>
                     <th>Deadline</th>
                     <th>Mobile</th>
                     <th>Status</th>
                     <th>Order Date</th>
                </tr>
                <?php 
                foreach ($rows as $row) {
                   echo '<tr>
                   <td>'.$row['firstname'].' '.$row['lastname'].'</td>
                   <td>'.$row['accepted_by'].'</td>
                   <td>'.$row['paid_to_worker'].'</td>
                   <td>'.$row['orderid'].'</td>
                   <td><img class="card-img-top" src="admin services/'.$row['image_url'].'" alt="Card image cap" height="30" style="width:auto;"></td>
                   <td>'.$row['service_name'].'</td>
                   <td>'.$row['amount'].' INR</td>
                   <td>'.$row['deadline'].'</td>
                   <td>'.$row['mobile'].'</td>
                   <td>'.$row['work_status'].'</td>
                   <td>'.$row['payment_date'].'</td>
                   </tr>';
                }
                ?> 
            </table> 
        </div>
    </div>
</div>
</body>
</html>
