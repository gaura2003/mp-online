<?php
// service_details.php

include './php/connection.php';

$service_id = $_GET['id'] ?? null;
$service = null;

// Fetch service details by ID
if ($service_id) {
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();
?>

<!-- Display service details -->
<div class="service-details">
    <?php if ($service): ?>
        <img class="card-img-top" src="admin services/<?php echo htmlspecialchars($service['image_url']); ?>" alt="Card image cap">
        <h2><?php echo $service['service_name']; ?></h2>
        <p><?php echo $service['description']; ?></p>
        <p>Price: &#x20b9;<?php echo $service['service_price']; ?></p>
        <form action="checkout.php" method="POST">
            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
            
            <input type="hidden" name="service_price" value="<?php echo $service['service_price']; ?>">
            <button type="submit">Order Service</button>
        </form>
    <?php else: ?>
        <p>Service not found.</p>
    <?php endif; ?>
</div>
