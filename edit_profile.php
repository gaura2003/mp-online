<?php
session_start();
include('./php/connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $service_type = $_POST['service_type'];
    $experience = $_POST['experience'];
    $availability = $_POST['availability'];

    $sql = "UPDATE users SET 
            username = ?, email = ?, phone_number = ?, address = ?, 
            service_type = ?, experience = ?, availability = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $email, $phone, $address,);
    
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
    $stmt->close();

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = "profile_" . $user_id . "." . $filetype;
            $upload_path = "../images/" . $new_filename;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $upload_path, $user_id);
                $stmt->execute();
                $stmt->close();
                $success_message .= " Profile picture updated.";
            } else {
                $error_message = "Error uploading profile picture.";
            }
        } else {
            $error_message = "Invalid file type. Please upload a JPG, JPEG, PNG, or GIF file.";
        }
    }
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="./Worker/css/edit_profile.css">
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
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Current Profile Picture" class="current-profile-picture">
                <?php endif; ?>
            </div>
            <!-- Rest of the form fields -->
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
           
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>
</body>
</html>
