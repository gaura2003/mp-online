   
   <?php

include './php/connection.php';

$user_role = $_SESSION['role'] ?? null;
$is_logged_in = isset($_SESSION['user_id']); // Check if the user is logged in

// Initialize the services array
$services = [];

// Check if there is a search query
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Retrieve services from the database
if (!empty($search_query)) {
    // Search for services based on the query
    $stmt = $conn->prepare("SELECT * FROM services WHERE service_name LIKE ?");
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_term);
} else {
    // Get all services if no search query
    $stmt = $conn->prepare("SELECT * FROM services");
}

$stmt->execute();
$result = $stmt->get_result();

// Check if there are services available
if ($result->num_rows > 0) {
    $services = $result->fetch_all(MYSQLI_ASSOC);
}

$stmt->close();
$conn->close();
?>
 <div id="content">
    
    <div class="header">
        <h1>Welcome to Online Services</h1>
        <form class="search-form" method="GET" action="">
            <input type="text" name="search" id="searchInput" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="main-content">
        <div class="left-content"></div>
        <div class="right-content"></div>
    </div>
    <div class="services-header">
        <h1>Services</h1>
        <select>
            <option value="">1</option>
            <option value="">2</option>
            <option value="">3</option>
        </select>
    </div>
    <div class="services">
        <?php if (!empty($services)): ?>
        <?php foreach ($services as $service): ?>
        <div class="service-card grid-item" data-service-id="<?php echo $service['id']; ?>" onclick="handleServiceClick(<?php echo $service['id']; ?>)">
            <img src="./admin services/<?php echo $service['image_url']; ?>" alt="<?php echo $service['service_name']; ?>">
            <div class="card-content">
                <h3><?php echo $service['service_name']; ?></h3>
                <p>&#x20b9;<?php echo $service['service_price']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p>No services found.</p>
        <?php endif; ?>
    </div>
    <div class="spacer"></div>
  

</div>

<div id="authModal" class="modal">
        <div class="modal-content" style="width:30%; height:80vh;">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="register-page">
                <h2 style="text-align: center;">Register</h2>
                <form method="POST" action="register.php">
                    <input type="hidden" id="role" name="role" value="">
                    <input class="form-control form-control-lg" type="text" id="name" name="name" placeholder="Enter Your Name" required>
                    <input class="form-control form-control-lg" type="text" id="name" name="full_name" placeholder="Enter Your Name" required>
                    <input class="form-control form-control-lg" type="tel" id="phone" name="phone" placeholder="Enter Phone Number">
                    <input class="form-control form-control-lg" type="email" id="email" name="email" required placeholder="Enter Email">
                    <input class="form-control form-control-lg" type="password" id="password" name="password" required placeholder="Enter Password">
                    <input class="form-control form-control-lg" id="address" name="address" placeholder="Enter Address">
                    <button type="submit" class="btn btn-primary mb-3 mt-4">Register</button>
                    <p>Already Have An Account? <span onclick="toggleAuthForms()" class="text-primary">Login Here</span></p>
                </form>
            </div>
            <div class="login-page">
                <h2 style="text-align: center;">Login</h2>
                <form action="login.php" method="POST">
                    <input class="form-control form-control-lg" type="text" id="username" name="email" placeholder="Enter Email" required>
                    <input class="form-control form-control-lg" type="password" id="password" name="password" required placeholder="Password">
                    <button type="submit" class="btn btn-primary mb-3 mt-4">Login</button>
                    <p>Don't Have An Account? </p>
                    <span onclick="toggleAuthForms()" class="text-primary"><button onclick="openRegisterModal('user')">Register as User</button>
<button onclick="openRegisterModal('worker')">Register as Worker</button>
</span>
                    
                </form>
            </div>
        </div>
    </div>


    <script>
   function handleServiceClick(serviceId) {
    <?php if ($is_logged_in): ?>
        const role = "<?php echo $user_role; ?>";
        if (role === "worker") {
            alert("Workers cannot access this service.");
        } else {
            window.location.href = 'service_details.php?id=' + serviceId;
        }
    <?php else: ?>
        openLoginModal();
    <?php endif; ?>
}

    function toggleAuthForms() {
        var registerPage = document.querySelector('.register-page');
        var loginPage = document.querySelector('.login-page');

        if (registerPage.style.display === 'none') {
            registerPage.style.display = 'block';
            loginPage.style.display = 'none';
        } else {
            registerPage.style.display = 'none';
            loginPage.style.display = 'block';
        }
    }

    function openRegisterModal(role) {
        document.getElementById('role').value = role;
        document.getElementById('authModal').style.display = 'block';
        document.querySelector('.register-page').style.display = 'block';
        document.querySelector('.login-page').style.display = 'none';
    }

    function openLoginModal() {
        document.getElementById('authModal').style.display = 'block';
        document.querySelector('.login-page').style.display = 'block';
        document.querySelector('.register-page').style.display = 'none';
    }

    function closeModal() {
        document.getElementById('authModal').style.display = 'none';
    }

    function searchServices() {
        var input = document.getElementById('searchInput').value.toLowerCase();
        var services = document.getElementsByClassName('grid-item');

        for (var i = 0; i < services.length; i++) {
            var nameElement = services[i].getElementsByTagName('h3')[0];
            var name = nameElement.innerText.toLowerCase();

            if (name.includes(input)) {
                services[i].style.display = 'block';
                var regex = new RegExp('(' + input + ')', 'gi');
                nameElement.innerHTML = name.replace(regex, '<span class="highlight">$1</span>');
            } else {
                services[i].style.display = 'none';
                nameElement.innerHTML = nameElement.innerText;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        searchServices();
    });

    document.getElementById('searchInput').addEventListener('input', searchServices);

   
</script>
