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
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include('../php/connection.php');
include('includes/header.php');


// Fetch services
$sql = "SELECT * FROM services";
$result = $conn->query($sql);
?>
<div class="main-content">
    <h1>Manage Services</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Service</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php while($service = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $service['id']; ?></td>
            <td><?php echo $service['service_name']; ?></td>
            <td><?php echo $service['description']; ?></td>
            <td>
                <a href="edit_service.php?id=<?php echo $service['id']; ?>">Edit</a>
                <a href="delete_service.php?id=<?php echo $service['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include('includes/footer.php'); ?>
