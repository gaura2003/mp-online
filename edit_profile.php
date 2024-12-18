<?php
session_start();
include('./php/connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission for updating user profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $role = $_POST['role']; // assuming role is also editable

    // Handle profile picture upload if a new one is selected
    $profile_picture = $user['profile_picture']; // Keep the old picture if not updating
    if ($_FILES['profile_picture']['error'] == 0) {
        // Check for valid image file type (optional)
        $image_tmp = $_FILES['profile_picture']['tmp_name'];
        $image_name = $_FILES['profile_picture']['name'];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $valid_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array(strtolower($image_ext), $valid_ext)) {
            // Define the file upload path
            $upload_dir = 'uploads/profile_pictures/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $new_image_name = time() . '.' . $image_ext;
            $image_path = $upload_dir . $new_image_name;
            
            // Move the uploaded file to the desired directory
            if (move_uploaded_file($image_tmp, $image_path)) {
                $profile_picture = $image_path;
            } else {
                $error_message = "Error uploading profile picture.";
            }
        } else {
            $error_message = "Invalid image format.";
        }
    }

    // Update query with the profile picture
    $sql_update = "UPDATE users SET username = ?, full_name = ?, email = ?, phone_number = ?, address = ?, role = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sssssssi", $username, $full_name, $email, $phone_number, $address, $role, $profile_picture, $user_id);

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        /* Style your form and page here */
    </style>
</head>
<body>
    <div class="edit-profile-container">
        <h1>Edit Profile</h1>

        <?php
        if (isset($success_message)) {
            echo "<p class='success-message'>$success_message</p>";
        }
        if (isset($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>

        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Current Profile Picture" style="width:100px;height:100px;margin-top:10px;">
            </div>

            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>
</body>
</html>
