<?php
session_start();
include('./php/connection.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['name'];
    $full_name = $_POST['full_name'] ?? null;
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $phone_number = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role']; // Assuming the role is provided in the form as 'user' or 'worker'

    // Check if email already exists in the users or workers table
    $sql_check = "SELECT email FROM users WHERE email = ? UNION SELECT email FROM mp_online_service_worker WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param('ss', $email, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "Email already exists.";
        } else {
            // Insert into appropriate table based on role
            if ($role === 'user') {
                $sql_insert_user = "INSERT INTO users (username, full_name, email, password, phone_number, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert_user = $conn->prepare($sql_insert_user);
                if ($stmt_insert_user) {
                    $stmt_insert_user->bind_param('sssssss', $username, $full_name, $email, $password, $phone_number, $address, $role);

                    if ($stmt_insert_user->execute()) {
                        echo "User registration successful!";
                        // Redirect or handle post-registration actions here
                    } else {
                        echo "Error: " . $stmt_insert_user->error;
                    }

                    $stmt_insert_user->close();
                } else {
                    echo "Error preparing statement for user registration.";
                }
            } elseif ($role === 'worker') {
                $sql_insert_worker = "INSERT INTO mp_online_service_worker (username, full_name, email, password, phone_number, address) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_insert_worker = $conn->prepare($sql_insert_worker);
                if ($stmt_insert_worker) {
                    $stmt_insert_worker->bind_param('ssssss', $username, $full_name, $email, $password, $phone_number, $address);

                    if ($stmt_insert_worker->execute()) {
                        echo "Worker registration successful!";
                        // Redirect or handle post-registration actions here
                    } else {
                        echo "Error: " . $stmt_insert_worker->error;
                    }

                    $stmt_insert_worker->close();
                } else {
                    echo "Error preparing statement for worker registration.";
                }
            } else {
                echo "Invalid role specified.";
            }
        }

        $stmt_check->close();
    } else {
        echo "Error preparing statement for email check.";
    }
    $conn->close();
}
?>
