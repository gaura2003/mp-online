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

// Update user profile logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    
    // Handle profile picture upload
    if ($_FILES['profile_picture']['error'] == 0) {
        $profile_picture = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    } else {
        $profile_picture = $user['profile_picture']; // Keep the old picture if no new one is uploaded
    }

    // Update user in the database
    $update_sql = "UPDATE users SET username = ?, email = ?, phone_number = ?, address = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssi", $username, $email, $phone_number, $address, $profile_picture, $user_id);
    $stmt->execute();

    // Refresh the user data after update
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Tailwind in PHP</title>
</head>
<body>
    <div class="text-center text-blue-500">Hello, Tailwind!</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        /* General Styles */
        .profile-card {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .profile-card img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }

        .profile-card h2 {
            font-size: 20px;
            margin: 10px 0 5px;
        }

        .profile-card p {
            color: #888;
            margin: 5px 0;
        }

        .profile-card .info {
            text-align: left;
            margin-top: 20px;
        }

        .profile-card .info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .profile-card .info div span {
            color: #333;
        }

        .profile-card .info div i {
            color: #888;
            cursor: pointer;
        }

        .edit-fields {
            display: none;
            margin-top: 20px;
        }

        .edit-fields input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .fas.fa-pen {
            cursor: pointer;
            margin-left: 10px;
            color: #007bff;
            float: right;
        }
    
    </style>
</head>

<body>
    <div class="profile-card">
        <i class="fas fa-pen" id="edit-icon"></i>
        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
        <h2><?php echo htmlspecialchars($user['username']); ?> 
        </h2>
        <p>@<?php echo htmlspecialchars($user['username']); ?></p>

        <!-- Information Section -->
        <div class="info" id="info-section">
            <div>
                <span>Username</span>
                <span><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div>
                <span>Email</span>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div>
                <span>Phone</span>
                <span><?php echo htmlspecialchars($user['phone_number']); ?></span>
            </div>
            <div>
                <span>Address</span>
                <span><?php echo htmlspecialchars($user['address']); ?></span>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="edit-field" id="edit-field">
            <form method="POST" action="profile.php" enctype="multipart/form-data">
                <label for="username">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label for="email">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="phone_number">Phone</label>
                <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

                <label for="address">Address</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>

                <label for="profile_picture">Profile Picture</label>
                <input type="file" name="profile_picture">

                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        const editIcon = document.getElementById('edit-icon');
        const infoSection = document.getElementById('info-section');
        const editFields = document.getElementById('edit-fields');

        function toggleEditMode() {
            infoSection.classList.toggle('hidden');
            editFields.classList.toggle('hidden');
        }
        toggleEditMode();
        // Add event listener for form submission
        editFields.addEventListener('submit', toggleEditMode);
        // Add event listener for cancel button
        const cancelButton = editFields.querySelector('button:last-child');
        cancelButton.addEventListener('click', toggleEditMode);
        // Add event listener for profile picture upload
        const profilePictureInput = editFields.querySelector('input[type="file"]');
        profilePictureInput.addEventListener('change', toggleEditMode);
        // Add event listener for pen icon
        const penIcon = document.querySelector('.fa-pen');
        penIcon.addEventListener('click', toggleEditMode);
        // Add event listener for escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                toggleEditMode();
            }
        });
        // Add event listener for clicking outside the edit fields
        document.addEventListener('click', function (event) {
            if (!editFields.contains(event.target)) {
                toggleEditMode();
            }
        });
        // Add event listener for clicking outside the edit icon
        document.addEventListener('click', function (event) {
            if (!editIcon.contains(event.target)) {
                toggleEditMode();
            }
        });
        // Add event listener for clicking outside the profile picture input
        document.addEventListener('click', function (event) {
            if (!profilePictureInput.contains(event.target)) {
                toggleEditMode();
            }
        });

    </script>
</body>

</html>
