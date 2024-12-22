<?php
// Include database connection
include 'db_connection.php';

// Start session
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to be logged in to delete your account.";
    exit;
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Check if the user confirms deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete the user's data from relevant tables (e.g., users, services, orders, etc.)
    try {
        // Begin transaction to ensure data integrity
        $pdo->beginTransaction();

        // Deleting user's related data from other tables (if necessary)
        $stmt1 = $pdo->prepare("DELETE FROM orders WHERE user_id = :user_id");
        $stmt1->bindParam(':user_id', $user_id);
        $stmt1->execute();

        $stmt2 = $pdo->prepare("DELETE FROM services WHERE user_id = :user_id"); // Modify this query as needed
        $stmt2->bindParam(':user_id', $user_id);
        $stmt2->execute();

        // Finally, delete the user's account
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Commit the transaction if everything is successful
        $pdo->commit();

        // Destroy session and redirect user
        session_destroy();
        header("Location: goodbye.php"); // Redirect to a page confirming account deletion
        exit;

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-5">
    <h2 class="text-2xl font-semibold text-center mb-5">Are you sure you want to delete your account?</h2>
    <form action="delete_account.php" method="POST" class="w-full max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <p class="text-red-600 mb-4">This action is irreversible. Once your account is deleted, all your data will be lost.</p>
        <div class="flex justify-between mb-4">
            <button type="submit" class="bg-red-600 text-white p-2 rounded w-1/2 mr-2">Yes, Delete My Account</button>
            <a href="settings.php" class="bg-gray-600 text-white p-2 rounded w-1/2 ml-2">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
