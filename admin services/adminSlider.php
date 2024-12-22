<?php
// Include database connection
include '../php/connection.php';

// Fetch all slides from the database
$sql = "SELECT * FROM slides";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Slides</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Slides</h2>
        <a href="add_slide.php" class="btn btn-primary mb-3">Add New Slide</a>
        
        <div class="carousel slide" id="carouselExampleControls" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $activeClass = 'active'; // To make the first slide active
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="carousel-item ' . $activeClass . '">
                        <img src="' . $row['image_url'] . '" class="d-block w-100" alt="' . $row['title'] . '">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>' . $row['title'] . '</h5>
                            <p>' . $row['description'] . '</p>
                        </div>
                    </div>';
                    $activeClass = ''; // Remove the active class for subsequent slides
                }
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Image URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result->data_seek(0); // Reset result pointer
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <tr>
                        <td>' . $row['title'] . '</td>
                        <td>' . $row['description'] . '</td>
                        <td>' . $row['image_url'] . '</td>
                        <td>
                            <a href="edit_slide.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_slide.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
