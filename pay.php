<?php
require __DIR__ . '/vendor/autoload.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('location:index.php'); 
    exit();
}

if (!isset($_SESSION['serviceid']) || empty($_SESSION['serviceid'])) {
    echo "Error: Service ID is not set.";
    exit();
}

$pid = $_SESSION['serviceid'];

// Include database connection
require_once 'php/connection.php';

// Razorpay API integration
include("gateway-config.php");

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

// User details
$firstname = $_SESSION['fname'];
$lastname = $_SESSION['lname'];
$email = $_SESSION['email'];
$mobile = $_SESSION['mobile'];
$address = $_SESSION['address'];
$note = $_SESSION['note'];
$deadline = $_SESSION['deadline'];

// Fetch service details with mysqli
$sql = "SELECT * FROM services WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $pid); // "i" indicates an integer parameter
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "Error: No service found for the given ID.";
    exit();
}

// Service price validation
$price = $row['service_price'];
if ($price <= 0) {
    echo "Error: Invalid price.";
    exit();
}

$_SESSION['price'] = $price;
$title = $row['service_name'];
$webtitle = 'Service station';
$imageurl = 'https://servicstation.com/assets/images/Avatar.png';

// Define $amount (price in paise for Razorpay)
$amount = $price * 100; // Razorpay accepts amount in paise

// Razorpay order creation
$orderData = [
    'receipt' => '3456',
    'amount' => $amount,
    'currency' => 'INR',
    'payment_capture' => 1 
];

$razorpayOrder = $api->order->create($orderData);
$razorpayOrderId = $razorpayOrder['id'];
$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayCurrency = 'INR'; // Default display currency
$displayAmount = $amount / 100; // Default display amount in INR

// Currency conversion handling (optional)
if ($displayCurrency !== 'INR') {
    $url = "https://api.exchangerate-api.com/v4/latest/INR"; // Alternative API URL
    $response = @file_get_contents($url);

    if ($response) {
        $exchange = json_decode($response, true);
        if (isset($exchange['rates'][$displayCurrency])) {
            $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
        } else {
            echo "Error: Unable to fetch exchange rate for {$displayCurrency}.";
        }
    } else {
        echo "Error: Currency conversion API failed.";
    }
}

// Data for Razorpay checkout
$data = [
    "key" => $keyId,
    "amount" => $amount,
    "name" => $webtitle,
    "description" => $title,
    "image" => $imageurl,
    "prefill" => [
        "name" => $firstname . ' ' . $lastname,
        "email" => $email,
        "contact" => $mobile,
    ],
    "notes" => [
        "address" => $address,
        "merchant_order_id" => "12312321",
        "deadline" => $deadline,
    ],
    "theme" => [
        "color" => "#F37254"
    ],
    "order_id" => $razorpayOrderId,
];

if ($displayCurrency !== 'INR') {
    $data['display_currency'] = $displayCurrency;
    $data['display_amount'] = $displayAmount;
}

$json = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pay for <?php echo htmlspecialchars($row['service_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12 form-container">
            <h1>Payment</h1>
            <hr>
            <div class="row">
                <div class="col-8">
                    <h4>(Payer Details)</h4>
                    <div class="mb-3">
                        <label class="label">First Name:</label> <?php echo htmlspecialchars($firstname); ?>
                    </div>
                    <div class="mb-3">
                        <label class="label">Last Name:</label> <?php echo htmlspecialchars($lastname); ?>
                    </div>
                    <div class="mb-3">
                        <label class="label">Email:</label> <?php echo htmlspecialchars($email); ?>
                    </div>
                    <div class="mb-3">
                        <label class="label">Mobile:</label> <?php echo htmlspecialchars($mobile); ?>
                    </div>
                    <div class="mb-3">
                        <label class="label">Address:</label> <?php echo htmlspecialchars($address); ?>
                    </div>
                    <div class="mb-3">
                        <label class="label">Note:</label> <?php echo htmlspecialchars($note); ?>
                    </div>
                    <div class="mb-3">
                        <label class="label">Deadline:</label> <?php echo htmlspecialchars($deadline); ?>
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div class="card" style="width: 18rem;">
                        <img class="card-img-top" src="admin services/<?php echo htmlspecialchars($row['image_url']); ?>" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['service_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['service_price']); ?> INR</p>
                        </div>
                    </div>
                    <br>
                    <center>
                        <form action="verify.php" method="POST">
                            <script
                                src="https://checkout.razorpay.com/v1/checkout.js"
                                data-key="<?php echo htmlspecialchars($data['key']); ?>"
                                data-amount="<?php echo htmlspecialchars($data['amount']); ?>"
                                data-currency="INR"
                                data-name="<?php echo htmlspecialchars($data['name']); ?>"
                                data-image="<?php echo htmlspecialchars($data['image']); ?>"
                                data-description="<?php echo htmlspecialchars($data['description']); ?>"
                                data-prefill.name="<?php echo htmlspecialchars($data['prefill']['name']); ?>"
                                data-prefill.email="<?php echo htmlspecialchars($data['prefill']['email']); ?>"
                                data-prefill.contact="<?php echo htmlspecialchars($data['prefill']['contact']); ?>"
                                data-notes.shopping_order_id="<?php echo htmlspecialchars($pid); ?>"
                                data-notes.deadline="<?php echo htmlspecialchars($deadline); ?>"
                                data-order_id="<?php echo htmlspecialchars($data['order_id']); ?>"
                                <?php if ($displayCurrency !== 'INR') { ?>
                                    data-display_amount="<?php echo htmlspecialchars($data['display_amount']); ?>"
                                    data-display_currency="<?php echo htmlspecialchars($data['display_currency']); ?>"
                                <?php } ?>
                            ></script>
                            <input type="hidden" name="shopping_order_id" value="<?php echo htmlspecialchars($pid); ?>">
                        </form>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
