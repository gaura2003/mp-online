<?php
// Include your database connection file
include './php/connection.php'; // connection.php should contain your database connection logic

// Check if a category is selected and a search term is provided
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';

// Build the SQL query
$sql = "SELECT s.id, s.service_name, s.description, s.image_url, s.service_price, s.created_at 
        FROM services s";

// Filters for category and search term
$whereClauses = [];
if ($category_id) {
    $whereClauses[] = "s.category_id = :category_id";
}
if ($search_term) {
    $whereClauses[] = "(s.service_name LIKE :search_term OR s.description LIKE :search_term)";
}

// Add the filters to the query if they exist
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Prepare and execute the SQL statement
$stmt = $pdo->prepare($sql);

// Bind parameters
if ($category_id) {
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
}
if ($search_term) {
    $stmt->bindValue(':search_term', '%' . $search_term . '%', PDO::PARAM_STR);
}

$stmt->execute();

// Fetch the results
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for the dropdown
$categories = $pdo->query("SELECT * FROM service_categories")->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="container mx-auto p-5 h-auto flex flex-col">
        <!-- Search and Category Form -->
        <form action="" method="GET" class="mb-5">
            <label for="category_id" class="mr-2">Select Category:</label>
            <select name="category_id" id="category_id" class="p-2 border rounded">
                <option value="">--Select Category--</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id']; ?>" <?= ($category['id'] == $category_id) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="search_term" class="ml-4 mr-2">Search Service:</label>
            <input type="text" name="search_term" id="search_term" class="p-2 border rounded" placeholder="Search for services" value="<?= htmlspecialchars($search_term); ?>">

            <button type="submit" class="ml-2 p-2 bg-blue-500 text-white rounded">Search</button>
        </form>

        <!-- Display Services -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($services): ?>
                <?php foreach ($services as $service): ?>
                    <div class="p-4 border rounded shadow-lg bg-white">
                        <img src="<?= htmlspecialchars($service['image_url']); ?>" alt="<?= htmlspecialchars($service['service_name']); ?>" class="w-full h-48 object-cover rounded">
                        <h3 class="mt-4 text-lg font-semibold"><?= htmlspecialchars($service['service_name']); ?></h3>
                        <p class="text-gray-600"><?= htmlspecialchars($service['description']); ?></p>
                        <p class="mt-2 text-xl font-semibold">₹<?= number_format($service['service_price'], 2); ?></p>
                        <p class="text-sm text-gray-500">Created on: <?= htmlspecialchars($service['created_at']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center col-span-full text-red-500">No services found.</p>
            <?php endif; ?>
        </div>
    </div>

