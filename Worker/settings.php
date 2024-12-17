<?php
include('./php/connection.php');
session_start();

// Check if worker_id is provided in the URL
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'worker') {
    $worker_id = $_SESSION['user_id'];

    // Prepare the SQL statement to fetch worker data
    $stmt = $conn->prepare("SELECT * FROM mp_online_service_worker WHERE id = ?");
    $stmt->bind_param("i", $worker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $worker = $result->fetch_assoc();
    } else {
        echo "No worker found with this ID.";
        exit;
    }

    $stmt->close();
} else {
    echo "No worker ID provided.";
    exit;
}

$conn->close();
?>
<div class="settings-container">
    <h1>Settings</h1>
    
    <form id="settingsForm">
        <div class="setting-group">
            <h2>Account Settings</h2>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $worker['email']; ?>">
            
            <label for="password">Change Password:</label>
            <input type="password" id="password" name="password">
            
            <label for="confirm-password">Confirm New Password:</label>
            <input type="password" id="confirm-password" name="confirm-password">
        </div>
        
        <div class="setting-group">
            <h2>Notification Preferences</h2>
            <label>
                <input type="checkbox" name="email_notifications" value="1"> Receive email notifications
            </label>
            <label>
                <input type="checkbox" name="sms_notifications" value="1"> Receive SMS notifications
            </label>
        </div>
        
        <div class="setting-group">
            <h2>Language Preference</h2>
            <select name="language">
                <option value="en">English</option>
                <option value="es">Español</option>
                <option value="fr">Français</option>
            </select>
        </div>
        
        <button type="submit" class="save-settings">Save Settings</button>
    </form>
</div>

<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Here you would typically send an AJAX request to save the settings
    alert('Settings saved successfully!');
});
</script>

<style>
.settings-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.setting-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-top: 10px;
}

input[type="email"],
input[type="password"],
select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
}

.save-settings {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
}

.save-settings:hover {
    background-color: #45a049;
}
</style>
