<?php
session_start();
include('./php/connection.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? null;
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $phone_number = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role']; // Assuming the role is provided in the form as 'user' or 'worker'

    // Generate base username from the first name in full_name
    $name_parts = explode(' ', trim($full_name));
    $base_username = strtolower($name_parts[0]); // Use the first name in lowercase
    $username = $base_username;

    // Check for unique username in both tables
    $counter = 1;
    $is_unique = false;

    while (!$is_unique) {
        $sql_check_username = "SELECT username FROM users WHERE username = ? UNION SELECT username FROM mp_online_service_worker WHERE username = ?";
        $stmt_check_username = $conn->prepare($sql_check_username);
        if ($stmt_check_username) {
            $stmt_check_username->bind_param('ss', $username, $username);
            $stmt_check_username->execute();
            $result_check_username = $stmt_check_username->get_result();

            if ($result_check_username->num_rows > 0) {
                // If username exists, append a counter to make it unique
                $username = $base_username . $counter;
                $counter++;
            } else {
                $is_unique = true;
            }

            $stmt_check_username->close();
        } else {
            echo "Error preparing statement for username check.";
            exit();
        }
    }

    // Check if email already exists in the users or workers table
    $sql_check_email = "SELECT email FROM users WHERE email = ? UNION SELECT email FROM mp_online_service_worker WHERE email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    if ($stmt_check_email) {
        $stmt_check_email->bind_param('ss', $email, $email);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();

        if ($result_check_email->num_rows > 0) {
            echo "Email already exists.";
        } else {
            // Insert into appropriate table based on role
            if ($role === 'user') {
                $sql_insert_user = "INSERT INTO users (username, full_name, email, password, phone_number, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert_user = $conn->prepare($sql_insert_user);
                if ($stmt_insert_user) {
                    $stmt_insert_user->bind_param('sssssss', $username, $full_name, $email, $password, $phone_number, $address, $role);

                    if ($stmt_insert_user->execute()) {
                        // Set session variables
                        $_SESSION['user_id'] = $stmt_insert_user->insert_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['full_name'] = $full_name;
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $role;

                        header('Location: /github clone/mp-online'); 
                        exit();
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
                        // Set session variables
                        $_SESSION['user_id'] = $stmt_insert_worker->insert_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['full_name'] = $full_name;
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $role;

                        header('Location: worker');
                        exit();
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

        $stmt_check_email->close();
    } else {
        echo "Error preparing statement for email check.";
    }
    $conn->close();
}
?>
