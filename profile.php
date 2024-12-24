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
    <title>User Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="bg-white border border-gray-300 rounded-lg p-5 text-center w-full max-w-md shadow-md mx-auto mt-12 ">
        <i class="fas fa-pen float-right text-blue-600 ml-3 cursor-pointer" id="edit-icon"></i>

        <div class="mt-10">
            <!-- Initially, show image and hide file input -->
            <img id="profile-img" src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="w-32 h-32 m-auto">
            <input type="file" name="profile_picture" id="profile-picture-input" class="mt-2 p-1 hidden">
            <p class="font-serif text-2xl font-bold">@<?php echo htmlspecialchars($user['username']); ?></p>
        </div>

        <table class="w-full flex justify-around">
            <thead>
                <tr class="flex flex-col">
                    <th class="p-1 text-right">Name :</th>
                    <th class="p-1 text-right">Email :</th>
                    <th class="p-1 text-right">Phone :</th>
                    <th class="p-1 text-right">Address :</th>
                </tr>
            </thead>

            <tbody id="updates" class="flex flex-col text-right">
                <form method="POST" enctype="multipart/form-data">
                    <tr class="flex flex-col">
                        <td><input class="border-none focus:border-b-2 focus:border-blue-300 p-1" value="<?= htmlspecialchars($user['username']); ?>" type="text" name="username" disabled></td>
                        <td><input class="border-none focus:border-b-2 focus:border-blue-300 p-1" value="<?= htmlspecialchars($user['email']); ?>" type="email" name="email" disabled></td>
                        <td><input class="border-none focus:border-b-2 focus:border-blue-300 p-1" value="<?= htmlspecialchars($user['phone_number']); ?>" type="tel" name="phone_number" disabled></td>
                        <td><input class="border-none focus:border-b-2 focus:border-blue-300 p-1" value="<?= htmlspecialchars($user['address']); ?>" type="text" name="address" disabled></td>
                    </tr>
                    <tr id="button-container" class="hidden">
                        <td colspan="4" class="text-right mt-4">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 px-4 py-2 text-white rounded">Save</button>
                            <button type="button" class="bg-gray-400 hover:bg-gray-600 px-4 py-2 text-white rounded" id="cancel-button">Cancel</button>
                        </td>
                    </tr>
                </form>
            </tbody>
        </table>
    </div>

    <script>
        const toggleIcon = document.getElementById('edit-icon');
        const buttonContainer = document.getElementById('button-container');
        const inputs = document.querySelectorAll('input');
        const cancelButton = document.getElementById('cancel-button');
        const form = document.querySelector('form');
        const profileImg = document.getElementById('profile-img');
        const profilePictureInput = document.getElementById('profile-picture-input');

        toggleIcon.addEventListener('click', function() {
            // Toggle button visibility
            buttonContainer.classList.toggle('hidden');
            // Toggle input fields enabled/disabled
            inputs.forEach(input => {
                input.disabled = !input.disabled;
            });
            // Toggle image visibility and file input
            profileImg.classList.toggle('hidden');
            profilePictureInput.classList.toggle('hidden');
        });

        cancelButton.addEventListener('click', function() {
            // Hide button container and disable inputs again
            buttonContainer.classList.add('hidden');
            inputs.forEach(input => {
                input.disabled = true;
            });
            // Revert to original image state
            profileImg.classList.remove('hidden');
            profilePictureInput.classList.add('hidden');
        });

        // Add event listener for profile picture upload
        profilePictureInput.addEventListener('change', function() {
            const file = profilePictureInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        // Load Content Dynamically
        function loadContent(page) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', page, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('content').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

    </script>
</body>

</html>