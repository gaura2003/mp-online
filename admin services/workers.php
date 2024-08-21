<style>
    body {
    font-family: Arial, sans-serif;
    margin: 20px;
}

h2 {
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
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

<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mp_online_services";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include('includes/header.php');

// Ensure only admin can access this page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
// Fetch all workers
$sql = "SELECT * FROM mp_online_service_worker";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Workers</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file link here -->
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
                <th>Role</th>
                <th>Status</th>
                <th>Balance</th>
                <th>Total Work Done</th>
                <th>Pending Work</th>
                <th>Completed Work</th>
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
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['full_name'] . "</td>";
                    echo "<td>" . $row['phone_number'] . "</td>";
                    echo "<td>" . $row['address'] . "</td>";
                    echo "<td>" . $row['role'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td>" . $row['balance'] . "</td>";
                    echo "<td>" . $row['total_work_done'] . "</td>";
                    echo "<td>" . $row['pending_work'] . "</td>";
                    echo "<td>" . $row['completed_work'] . "</td>";
                    echo "<td>" . $row['this_week_earning'] . "</td>";
                    echo "<td>" . $row['this_month_earning'] . "</td>";
                    echo "<td>" . $row['this_year_earning'] . "</td>";
                    echo "<td>
                            <form action='worker_actions.php' method='post'>
                                <input type='hidden' name='worker_id' value='" . $row['id'] . "'>
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
$conn->close();
?>
<?php include('includes/footer.php'); ?>