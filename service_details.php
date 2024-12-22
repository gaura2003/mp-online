<?php
// service_details.php

include './php/connection.php';

$service_id = $_GET['id'] ?? null;
$service = null;
$suggestions = [];

// Fetch service details by ID
if ($service_id) {
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
        
        // Fetch related services
        $document_list = json_decode($service['document_list'], true); // Decode JSON to array
        if (!empty($document_list)) {
            $placeholders = implode(' OR ', array_fill(0, count($document_list), "JSON_CONTAINS(document_list, ?)"));

            $query = "SELECT * FROM services WHERE id != ? AND ($placeholders)";
            $suggestion_stmt = $conn->prepare($query);
            
            // Prepare parameters dynamically
            $params = [$service_id];
            foreach ($document_list as $doc) {
                $params[] = json_encode([$doc]); // Encode each document as JSON
            }

            $types = str_repeat('s', count($document_list)) . 'i'; // String types for documents + integer for ID
            $suggestion_stmt->bind_param($types, ...$params);
            $suggestion_stmt->execute();

            $suggestion_result = $suggestion_stmt->get_result();
            while ($row = $suggestion_result->fetch_assoc()) {
                $suggestions[] = $row;
            }
            $suggestion_stmt->close();
        }
    }
    $stmt->close();
}

$conn->close();
?>


<!-- Display service details -->
<div class="container my-5 d-flex flex-column h-auto">
    <?php if ($service): ?>
        <div class="mb-4 shadow-sm d-flex p-2 gap-4">
        <div class="card mb-4 shadow-sm p-3">
            <img src="admin services/<?php echo htmlspecialchars($service['image_url']); ?>" class="card-img-top" alt="Service Image">
            <div class="card-body">
                <h1 class="card-title"><?php echo htmlspecialchars($service['service_name']); ?></h1>
                <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                <p class="mt-4 h4">Price: &#x20b9;<?php echo htmlspecialchars($service['service_price']); ?></p>
                <form action="checkout.php" method="POST" class="mt-3">
                    <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service['id']); ?>">
                    <input type="hidden" name="service_price" value="<?php echo htmlspecialchars($service['service_price']); ?>">
                    <button type="submit" class="btn btn-primary">
                        Order Service
                    </button>
                </form>
            </div>
        </div>
        <div>
        <?php
                $document_list = json_decode($service['document_list'], true); // Convert JSON to array

                if (!empty($document_list)): ?>
                    <h3 class="mt-4">Required Documents:</h3>
                    <ul class="list-group">
                        <?php foreach ($document_list as $document): ?>
                            <li class="list-group-item"><?php echo htmlspecialchars($document); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="mt-4">No documents required for this service.</p>
                <?php endif; ?>
        </div>
        </div>

          <!-- Suggested Services -->
          <?php if (!empty($suggestions)): ?>
            <h2 class="mb-4">Other Services You May Need:</h2>
            <div class="services">
                <?php foreach ($suggestions as $suggestion): ?>
                    <div class="service-card grid-item" data-service-id="<?php echo htmlspecialchars($suggestion['id']); ?>" onclick="handleServiceClick(<?php echo htmlspecialchars($suggestion['id']); ?>)">
                        <img src="./admin services/<?php echo htmlspecialchars($suggestion['image_url']); ?>" alt="<?php echo htmlspecialchars($suggestion['service_name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="card-content">
                            <h3 title="<?php echo htmlspecialchars($suggestion['service_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($suggestion['service_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </h3>
                            <p>&#x20b9;<?php echo number_format($suggestion['service_price'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No suggestions available for this service.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-center text-danger">Service not found.</p>
    <?php endif; ?>
</div>

<script>
function handleServiceClick(serviceId) {
    window.location.href = `service_details.php?id=${serviceId}`;
}
</script>
