<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include('connection.php');

$service_name = $_POST['service_name'];
$service_price = $_POST['service_price'];
$category_id = $_POST['category_id'];
$description = $_POST['description'];

// Handle image upload
$image_url = '';
$target_dir = "../images/";
$target_file = $target_dir . basename($_FILES["image_url"]["name"]);
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["image_url"]["tmp_name"]);
if($check !== false) {
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        exit;
    }
} else {
    echo "File is not an image.";
    exit;
}

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    exit;
}

// If everything is ok, try to upload file
if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $target_file)) {
    $image_url = $target_file;
} else {
    echo "Sorry, there was an error uploading your file.";
    exit;
}

// Insert service data into the database
$sql = "INSERT INTO services (category_id, service_name, description, image_url,service_price) 
        VALUES ('$category_id', '$service_name', '$description', '$image_url','$service_price')";

if ($conn->query($sql) === TRUE) {
    echo "New service added successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
