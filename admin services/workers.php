<?php
session_start();

// Include connection and header files
include '../php/connection.php';
include('includes/header.php');

// Ensure only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch all workers securely using prepared statements
$sql = "SELECT id, username, email, full_name, phone_number, address, 
               COALESCE(status, 'N/A') AS status,
               COALESCE(balance, 0.00) AS balance,
               COALESCE(total_work_done, 0) AS total_work_done,
               COALESCE(pending_work, 0) AS pending_work,
               COALESCE(completed_work, 0) AS completed_work,
               COALESCE(this_week_earning, 0.00) AS this_week_earning,
               COALESCE(this_month_earning, 0.00) AS this_month_earning,
               COALESCE(this_year_earning, 0.00) AS this_year_earning
        FROM mp_online_service_worker";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Workers</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file link here -->
    <style>
    
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 98%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            display: grid;
            grid-template-columns: auto auto;
        }
        
    </style>
</head>
<body>
    <h2>Manage Workers</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Full Name</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Status</th>
                <th>Balance</th>
                <th>Total Work Done</th>
                <th>PW</th>
                <th>CW</th>
                <th>This Week Earning</th>
                <th>This Month Earning</th>
                <th>This Year Earning</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Sanitize output to prevent XSS
                    $id = htmlspecialchars($row['id']);
                    $username = htmlspecialchars($row['username']);
                    $email = htmlspecialchars($row['email']);
                    $full_name = htmlspecialchars($row['full_name']);
                    $phone_number = htmlspecialchars($row['phone_number']);
                    $address = htmlspecialchars($row['address']);
                    $status = htmlspecialchars($row['status']);
                    $balance = htmlspecialchars($row['balance']);
                    $total_work_done = htmlspecialchars($row['total_work_done']);
                    $pending_work = htmlspecialchars($row['pending_work']);
                    $completed_work = htmlspecialchars($row['completed_work']);
                    $this_week_earning = htmlspecialchars($row['this_week_earning']);
                    $this_month_earning = htmlspecialchars($row['this_month_earning']);
                    $this_year_earning = htmlspecialchars($row['this_year_earning']);

                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$username</td>";
                    echo "<td>$email</td>";
                    echo "<td>$full_name</td>";
                    echo "<td>$phone_number</td>";
                    echo "<td>$address</td>";
                    echo "<td>$status</td>";
                    echo "<td>$balance</td>";
                    echo "<td>$total_work_done</td>";
                    echo "<td>$pending_work</td>";
                    echo "<td>$completed_work</td>";
                    echo "<td>$this_week_earning</td>";
                    echo "<td>$this_month_earning</td>";
                    echo "<td>$this_year_earning</td>";
                    echo "<td>
                            <form action='worker_actions.php' method='post'>
                                <input type='hidden' name='worker_id' value='$id'>
                                <button type='submit' name='action' value='block'>Block</button>
                                <button type='submit' name='action' value='unblock'>Unblock</button>
                                <button type='submit' name='action' value='activate'>Activate</button>
                                <button type='submit' name='action' value='deactivate'>Deactivate</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='16'>No workers found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
include('includes/footer.php');
?>
