<?php
// Include the database connection file
include './php/connection.php';

// Fetch all slides from the database
$sql = "SELECT * FROM slides";
$result = $conn->query($sql);
?>

<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        // Set the first slide to be active
        $activeClass = 'active';
        
        // Loop through the slides and display each one
        while ($row = $result->fetch_assoc()) {
            echo '
            <div class="carousel-item ' . $activeClass . '">
                <img src="' . $row['image_url'] . '" class="d-block w-100" alt="' . $row['title'] . '">
                <div class="carousel-caption d-none d-md-block">
                    <h5>' . $row['title'] . '</h5>
                    <p>' . $row['description'] . '</p>
                </div>
            </div>';
            
            // After the first slide, remove the 'active' class
            $activeClass = '';
        }
        ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

