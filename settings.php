<?php
session_start();

// Include database connection
include './php/connection.php';

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notifications_enabled = isset($_POST['notifications_enabled']) ? 1 : 0;
    $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;
    $language_preference = $_POST['language_preference'];

    // Update the user profile
    $stmt = $pdo->prepare("UPDATE users SET notifications_enabled = :notifications_enabled, dark_mode = :dark_mode, language_preference = :language_preference, updated_at = CURRENT_TIMESTAMP WHERE id = :user_id");
    $stmt->bindParam(':notifications_enabled', $notifications_enabled);
    $stmt->bindParam(':dark_mode', $dark_mode);
    $stmt->bindParam(':language_preference', $language_preference);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    echo "Profile updated successfully!";
}

if (isset($_POST['current_password']) && isset($_POST['new_password'])) {
    // Validate current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_data = $stmt->fetch();

    if (password_verify($_POST['current_password'], $user_data['password'])) {
        // Hash the new password and update it
        if ($_POST['new_password'] === $_POST['confirm_new_password']) {
            $new_password_hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
            $update_stmt->bindParam(':new_password', $new_password_hashed);
            $update_stmt->bindParam(':user_id', $user_id);
            $update_stmt->execute();

            echo "Password updated successfully!";
        } else {
            echo "New passwords do not match!";
        }
    } else {
        echo "Current password is incorrect!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-5" style="height: 700px;">
    <h2 class="text-2xl font-semibold text-center mb-5">Account Settings</h2>

    <form action="settings.php" method="POST" class="w-full max-w-lg mx-auto h-[600px] bg-white p-6 rounded-lg shadow-lg">

        <div class="mb-4">
            <label for="current_password" class="block text-gray-700">Current Password</label>
            <input type="password" name="current_password" id="current_password" placeholder="Current Password" required class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
            <label for="new_password" class="block text-gray-700">New Password</label>
            <input type="password" name="new_password" id="new_password" placeholder="Leave blank if you don't want to change" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
            <label for="confirm_new_password" class="block text-gray-700">Confirm New Password</label>
            <input type="password" name="confirm_new_password" id="confirm_new_password" placeholder="Leave blank if you don't want to change" class="w-full p-2 border rounded">
        </div>

        <!-- Language Preferences -->
        <div class="mb-4">
            <label for="language" class="block text-gray-700">Language Preferences</label>
            <select name="language_preference" id="language" class="w-full p-2 border rounded h-10">
                <option value="en" <?php echo $user['language_preference'] == 'en' ? 'selected' : ''; ?>>English</option>
                <option value="hi" <?php echo $user['language_preference'] == 'hi' ? 'selected' : ''; ?>>Hindi</option>
            </select>
        </div>

        <!-- Notification Preferences -->
        <div class="mb-4">
            <label class="block text-gray-700">Notification Preferences</label>
            <label for="notifications_enabled" class="inline-flex items-center">
                <input type="checkbox" name="notifications_enabled" id="notifications_enabled" class="form-checkbox h-5 w-5 text-blue-500" <?php echo $user['notifications_enabled'] ? 'checked' : ''; ?>>
                <span class="ml-2">Enable Notifications</span>
            </label>
        </div>

        <!-- Account Deletion -->
        <div class="mb-4">
            <label for="delete_account" class="block text-red-500">Delete Account</label>
            <button type="button" onclick="confirmDeletion()" class="w-full bg-red-600 text-white p-2 rounded">Delete My Account</button>
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Save Changes</button>
        </div>
    </form>
</div>

</body>
<script>
    // JavaScript function to confirm account deletion
    function confirmDeletion() {
        if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
            window.location.href = './php/delete_account.php'; // Redirect to delete account page
        }
    }
</script>
</html>
