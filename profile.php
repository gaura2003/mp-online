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
            display: flex;
            justify-content: space-between;
            width: 300px;
        }

        .profile-card .info div i {
            color: #888;
        }

        .edit-profile-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .edit-profile-btn:hover {
            background-color: #0056b3;
        }

        /* Media Queries for Responsiveness */
        /* For large screens like PC and Laptop */
        @media (min-width: 1024px) {
            .profile-card {
                max-width: 400px;
            }
        }

        /* For medium screens like Tablets */
        @media (max-width: 1024px) and (min-width: 768px) {
            .profile-card {
                max-width: 350px;
            }

            .profile-card h2 {
                font-size: 18px;
            }

            .profile-card p {
                font-size: 14px;
            }
        }

        /* For small screens like Phones */
        @media (max-width: 768px) {
            body {
                margin: 0;
                padding: 10px;
            }

            .profile-card {
                width: 100%;
                max-width: 90%;
                padding: 15px;
            }

            .profile-card img {
                width: 80px;
                height: 80px;
            }

            .profile-card h2 {
                font-size: 16px;
            }

            .profile-card p {
                font-size: 12px;
            }

            .edit-profile-btn {
                padding: 8px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="profile-card">
        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
        <p>@<?php echo htmlspecialchars($user['username']); ?> <i class="fas fa-pen"></i></p>

        <div class="info">
            <div>
                <span>Username</span>
                <span><?php echo htmlspecialchars($user['username']); ?> <i class="fas fa-pen"></i></span>
            </div>
            <div>
                <span>Email</span>
                <span><?php echo htmlspecialchars($user['email']); ?> <i class="fas fa-pen"></i></span>
            </div>
            <div>
                <span>Phone</span>
                <span><?php echo htmlspecialchars($user['phone_number']); ?> <i class="fas fa-pen"></i></span>
            </div>
            <div>
                <span>Address</span>
                <span><?php echo htmlspecialchars($user['address']); ?> <i class="fas fa-pen"></i></span>
            </div>
        </div>

        <a href="edit_profile.php" class="edit-profile-btn">Edit Profile</a>
    </div>
</body>

</html>