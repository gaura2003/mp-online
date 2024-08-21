<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin services/index.php");
    exit;
}

include('includes/header.php');
include('includes/sidebar.php');
?>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            width: 50%;
            margin: 0 auto;
            margin-bottom: 40px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="file"] {
            width: 100%;
            margin-top: 5px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
<div class="stats" style="display:flex; align-items:center; justify-content: center; flex-direction: column;">
    <div class="container">
        <form action="./includes/process_add_category.php" method="POST">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <button type="submit">Add Category</button>
        </form>
    </div>

    <div class="container">
        <form action="./includes/process_add_service.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="service_name">Service Name:</label>
                <input type="text" id="service_name" name="service_name" required placeholder="Service Name">
                <label for="service_name">Service Price:</label>
                <input type="text" id="service_price" name="service_price" required placeholder="Service Price">
            </div>
        
            <select name="category_id" required>
            <option value="" disabled selected>Select Category</option>
            <!-- PHP code to fetch categories from database and generate options -->
            <?php
            include('./includes/connection.php');

            $sql = "SELECT * FROM service_categories";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["category_id"] . "'>" . $row["category_name"] . "</option>";
                }
            } else {
                echo "<option value='' disabled>No categories available</option>";
            }

           
            ?>
        </select>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="image_url">Image URL:</label>
                <input type="file" id="image_url" name="image_url" accept="image/*" required>
            </div>
            <button type="submit">Add Service</button>
        </form>
    </div>

    <h1>add service request</h1>
    <div class="container">
        <form action="./includes/process_add_service_request.php" method="POST">
            <div class="form-group">
                <label for="user_id">User ID:</label>
                <input type="text" id="user_id" name="user_id" required>
            </div>
            <div class="form-group">
                <label for="service_id">Service ID:</label>
                <input type="text" id="service_id" name="service_id" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="in progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" rows="4"></textarea>
            </div>
            <button type="submit">Add Service Request</button>
        </form>
    </div>
    </div>
    <?php include('includes/footer.php'); ?>
