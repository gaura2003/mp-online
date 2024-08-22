<?php 
require_once("config.php"); 
$pid = $_GET['service_id'];
$sql = "SELECT count(*) from services WHERE id=:pid"; 
$stmt = $db->prepare($sql);
$stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count == 0) { 
    header('location:index.php'); 
    exit(); 
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - Service </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12 form-container">
            <h1>Checkout</h1>
            <hr>
            <?php 
            if (isset($_POST['submit_form'])) {
                $_SESSION['fname'] = $_POST['fname']; 
                $_SESSION['lname'] = $_POST['lname']; 
                $_SESSION['email'] = $_POST['email']; 
                $_SESSION['mobile'] = $_POST['mobile']; 
                $_SESSION['note'] = $_POST['note']; 
                $_SESSION['address'] = $_POST['address']; 
                $_SESSION['pid'] = $pid;
                $_SESSION['deadline'] = $_POST['deadline']; // Store the selected deadline in the session
                
                if ($_POST['email'] != '') {
                    header("location:pay.php");
                }
            } 
            ?>		
            <div class="row"> 
                <div class="col-8"> 
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="label">First Name</label>
                            <input type="text" class="form-control" name="fname" required>
                        </div>
                        <div class="mb-3">
                            <label class="label">Last Name</label>
                            <input type="text" class="form-control" name="lname" required>
                        </div>
                        <div class="mb-3">
                            <label class="label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="label">Mobile</label>
                            <input type="number" class="form-control" name="mobile" required>
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
                        <?php 
                        $sql = "SELECT * from services WHERE id=:pid"; 
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
                        $stmt->execute();
                        $row = $stmt->fetch();
                        echo '<div class="card" style="width: 18rem;">
                            <img class="card-img-top" src="admin services/' . htmlspecialchars($row['image_url']) . '" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row['service_name']) . '</h5>
                                <p class="card-text">' . htmlspecialchars($row['service_price']) . ' INR</p>
                            </div>
                        </div>';
                        ?> 
                        <br>
                        <button type="submit" class="btn btn-primary" name="submit_form">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
