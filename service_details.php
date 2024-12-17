<?php
session_start();
include('./php/connection.php');

// Check if a service ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$service_id = $_GET['id'];

// Retrieve service details from the database
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the service exists
if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$service = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $service['service_name']; ?></title>
    <link rel="stylesheet" href="./css/new.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .service-details {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<body>

<?php include('nav.php'); ?>
<div class="content">
    <div class="service-details mt-20">
        <div class="card w-44 ">
            <img src="./admin services/<?php echo $service['image_url']; ?>" class="card-img-top" alt="<?php echo $service['service_name']; ?>">
            <div class="card-body">
                <h5 class="card-title"><?php echo $service['service_name']; ?></h5>
                <p><strong>Price:</strong> &#x20b9;<?php echo $service['service_price']; ?></p>
                <p class="card-text"><?php echo $service['description']; ?></p>
                <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
            </div>
        </div>
        <button id="takeServiceButton1">Take Service</button>
        <button id="payButton">Pay</button>
    </div>
</div>

<script>
document.getElementById('takeServiceButton1').addEventListener('click', function() {
    <?php if (isset($_SESSION['user_id'])): ?>
        // Service details to be sent via WhatsApp
        const serviceName = "<?php echo $service['service_name']; ?>";
        const servicePrice = "<?php echo $service['service_price']; ?>";
        const serviceDescription = "<?php echo $service['description']; ?>";
        const message = `Hello, I am interested in the service "${serviceName}" which costs ₹${servicePrice}. Here are the details: ${serviceDescription}`;
        
        // Replace with the desired phone number (including country code)
        const phoneNumber = "919876543210";
        
        // Redirecting to WhatsApp with the message and phone number
        const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
        window.location.href = whatsappUrl;
    <?php else: ?>
        document.getElementById('loginModal').style.display = 'block';
    <?php endif; ?>
});

document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('loginModal').style.display = 'none';
});

window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('loginModal')) {
        document.getElementById('loginModal').style.display = 'none';
    }
});

document.getElementById('payButton').addEventListener('click', function(e) {
    e.preventDefault();
    
    window.location.href = `checkout.php?service_id=<?php echo $service_id; ?>`;
});
</script>
</body>
</html>
