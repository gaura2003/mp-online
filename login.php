<?php
session_start();
include('./php/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the query
    $sql = "
        SELECT id, username, password, role 
        FROM (
            SELECT id, username, password, role  -- Include role column here
            FROM users 
            WHERE email = ?
            
            UNION ALL
            
            SELECT id, username, password, 'worker' AS role 
            FROM mp_online_service_worker 
            WHERE email = ?
        ) AS combined
    ";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin services");
        } elseif ($user['role'] === 'worker') {
            header("Location: Worker");
        } else {
            header("Location: /github clone/mp-online");
        }
        exit();
    } else {
        echo "Invalid login credentials.";
    }

    $stmt->close();
    $conn->close();
}
?>
