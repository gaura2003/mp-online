<style>
    body {
    font-family: Arial, sans-serif;
    margin: 20px;
}

h2 {
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid black;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

form {
    display: inline-block;
}

</style>
<?php
session_start();

include('../php/connection.php');
include('includes/header.php');


// Check if the user is logged in as admin (role = 'admin')
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Handle actions based on query parameters
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $user_id = $_GET['id'];

    switch ($action) {
        case 'block':
            blockUser($user_id);
            break;
        case 'unblock':
            unblockUser($user_id);
            break;
        case 'activate':
            activateAccount($user_id);
            break;
        case 'deactivate':
            deactivateAccount($user_id);
            break;
        default:
            // Invalid action
            break;
    }
}

// Function to block a user
function blockUser($user_id) {
    global $conn;
    $sql = "UPDATE users SET status = 'blocked' WHERE user_id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "User blocked successfully.";
    } else {
        echo "Error blocking user: " . $conn->error;
    }
}

// Function to unblock a user
function unblockUser($user_id) {
    global $conn;
    $sql = "UPDATE users SET status = 'active' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "User unblocked successfully.";
    } else {
        echo "Error unblocking user: " . $conn->error;
    }
}

// Function to activate an account
function activateAccount($user_id) {
    global $conn;
    $sql = "UPDATE users SET status = 'active' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "Account activated successfully.";
    } else {
        echo "Error activating account: " . $conn->error;
    }
}

// Function to deactivate an account
function deactivateAccount($user_id) {
    global $conn;
    $sql = "UPDATE users SET status = 'inactive' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "Account deactivated successfully.";
    } else {
        echo "Error deactivating account: " . $conn->error;
    }
}

// Retrieve users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Check if there are users available
if ($result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $users = [];
}

$conn->close();
?>

    <main>
        <h2>Users List</h2>
        <div class="user-list">
            <?php if (!empty($users)): ?>
                <table>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['role']; ?></td>
                            <td><?php echo $user['status']; ?></td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <a href="?action=block&user_id=<?php echo $user['id']; ?>">Block</a> |
                                <?php elseif ($user['status'] === 'blocked'): ?>
                                    <a href="?action=unblock&user_id=<?php echo $user['id']; ?>">Unblock</a> |
                                <?php endif; ?>
                                <?php if ($user['status'] === 'inactive'): ?>
                                    <a href="?action=activate&user_id=<?php echo $user['id']; ?>">Activate</a>
                                <?php else: ?>
                                    <a href="?action=deactivate&user_id=<?php echo $user['id']; ?>">Deactivate</a>
                                <?php endif; ?>
                                <!-- Add more actions as needed -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
