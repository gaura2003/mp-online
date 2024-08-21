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

$search = isset($_GET['search']) ? $_GET['search'] : '';

// SQL query for searching
$sql = "SELECT * FROM services, payments 
        WHERE services.id = payments.pid 
        AND (firstname LIKE :search OR lastname LIKE :search OR payer_email LIKE :search OR orderid LIKE :search OR mobile LIKE :search)";

$stmt = $db->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$rows = $stmt->fetchAll();

// Display results
foreach ($rows as $row) {
    echo '<a href="#" class="list-group-item list-group-item-action">
        <strong>'.$row['firstname'].' '.$row['lastname'].'</strong><br>
        Email: '.$row['payer_email'].'<br>
        Order ID: '.$row['orderid'].'
    </a>';
}
?>
