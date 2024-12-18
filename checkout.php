<?php
// checkout.php
require('config.php');
include './php/connection.php';

$service_id = $_POST['service_id'] ?? null;
$service_price = $_POST['service_price'] ?? null;

// Fetch service details
$service = null;
if ($service_id && $service_price) {
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();
?>

<!-- Dynamic Checkout Form -->
<div class="checkout-container">
    <h1>Checkout - <?php echo htmlspecialchars($service['service_name']); ?></h1>
    <hr>

    <?php 
    if (isset($_POST['submit_form'])) {
        // Store form data in session variables
        $_SESSION['fname'] = $_POST['fname']; 
        $_SESSION['lname'] = $_POST['lname']; 
        $_SESSION['email'] = $_POST['email']; 
        $_SESSION['mobile'] = $_POST['mobile']; 
        $_SESSION['note'] = $_POST['note']; 
        $_SESSION['address'] = $_POST['address']; 
        $_SESSION['serviceid'] = $_POST['service_id']; // Store service ID in session
        $_SESSION['deadline'] = $_POST['deadline']; // Store the selected deadline in session
        
        // Redirect to payment page if email is provided
        if ($_POST['email'] != '') {
            header("location:pay.php");
            exit(); // Ensure no further code is executed after redirect
        }
    } 
    ?>		
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="row"> 
        <div class="col-8"> 
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="label">First Name</label>
                    <input type="text" class="form-control" name="fname" required>
                </div>
                <input type="hidden" name="service_id" value="<?php  echo $service_id ;?>">
                <div class="mb-3">
                    <label class="label">Last Name</label>
                    <input type="text" class="form-control" name="lname" required>
                </div>
                <div class="mb-3">
                    <label class="label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="label">Mobile</label>
                    <input type="phone" class="form-control" name="mobile" required>
                </div>
                <div class="mb-3">
                    <label class="label">Address</label>
                    <textarea name="address" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="label">Note</label>
                    <textarea name="note" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label class="label">Deadline</label>
                    <input type="date" class="form-control" name="deadline" required>
                </div>
        </div>

        <div class="col-4 text-center">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="admin services/<?php echo htmlspecialchars($service['image_url']); ?>" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($service['service_price']); ?> INR</p>
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-primary" name="submit_form">Place Order</button>
            </form>
        </div>
    </div>
</div>
