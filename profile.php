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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="./Worker/css/profile.css">
</head>
<body>
    <div class="profile-container">
        <h1>Profile</h1>
        <div class="profile-info">
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($user['username']); ?></h2>
            <p class="email"><?php echo htmlspecialchars($user['email']); ?></p>
            <p class="phone"><?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p class="address"><?php echo htmlspecialchars($user['address']); ?></p>
        </div>
        
        <a onclick="loadContent('edit_profile.php')" class="edit-profile-btn">Edit Profile</a>
    </div>
</body>
<script>
     function loadContent(page) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', page, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('content').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
</script>
</html>
