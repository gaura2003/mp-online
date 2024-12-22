<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<style>
    li{
        padding:10px;
    }
    li a{
        text-decoration:none;
        color:white;
        font-size:20px;
    }
    .table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 10px;
    border: 1px solid #ddd;
}

.table th {
    background: #f4f4f4;
}
</style>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <ul style="display:flex; list-style:none;">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="workers.php">Manage Workers</a></li>
            <li><a href="services.php">Manage Services</a></li>
            <li><a href="add services.php">Add services</a></li>
            <li><a href="payments.php">Manage orders</a></li>
            <li><a href="adminSlider.php">Manage Slider</a></li>
            <li><a href="../index.php">Go to Site</a></li>
        </ul>
        <div class="user-info">
            <?php echo $_SESSION['username']; ?> | <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="content">