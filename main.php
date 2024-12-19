<?php
include './php/connection.php';

$user_role = $_SESSION['role'] ?? null;
$is_logged_in = isset($_SESSION['user_id']); // Check if the user is logged in

// Initialize the services array
$services = [];

// Check if there is a search query
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // Retrieve services from the database
    if (!empty($search_query)) {
        // Search for services based on the query
        $stmt = $conn->prepare("SELECT * FROM services WHERE service_name LIKE ?");
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement.");
        }
        $search_term = "%" . $search_query . "%";
        $stmt->bind_param("s", $search_term);
    } else {
        // Get all services if no search query
        $stmt = $conn->prepare("SELECT * FROM services");
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement.");
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are services available
    if ($result->num_rows > 0) {
        $services = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</style>
<div id="content">
    <div class="header">
        <h1>Welcome to Online Services</h1>
        <form  method="GET" action="">
            <div class="search-container">
                <label class="search-label">
                    <div class="input-group">
                        <div class="icon-container">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="searchInput" placeholder="Find a service" class="search-input" value="<?php echo htmlspecialchars($search_query); ?>" />
                    </div>
                </label>
            </div>
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
                <!-- Service Image -->
                <img src="./admin services/<?php echo $service['image_url']; ?>" alt="<?php echo htmlspecialchars($service['service_name'], ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Service Content -->
                <div class="card-content">
                    <!-- Service Name -->
                    <h3 title="<?php echo htmlspecialchars($service['service_name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($service['service_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <!-- Service Price -->
                    <p>&#x20b9;<?php echo number_format($service['service_price'], 2); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No services found.</p>
    <?php endif; ?>

</div>
<div class="stats-section">
    <div class="stat-item">
        <ion-icon name="settings-outline"></ion-icon>
        <h2>20</h2>
        <p>Service Providers</p>
    </div>
    <div class="stat-item">
        <ion-icon name="lock-closed-outline"></ion-icon>
        <h2>100</h2>
        <p>Happy Customers</p>
    </div>
    <div class="stat-item">
    <ion-icon name="megaphone-outline"></ion-icon>
        <h2>350+</h2>
        <p>Successful orders</p>
    </div>
    <div class="stat-item">
        <ion-icon name="list-outline"></ion-icon>
        <h2>100+</h2>
        <p>Categories</p>
    </div>
</div>

<div id="authModal" class="modal">
    <div class="modal-content" style="width:30%; height:80vh;">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="register-page">
            <h2 style="text-align: center;">Register</h2>
            <form method="POST" action="register.php">
                <input type="hidden" id="role" name="role" value="">
                <input class="form-control form-control-lg" type="text" id="name" name="name" placeholder="Enter Your Name" required>
                <input class="form-control form-control-lg" type="text" id="full_name" name="full_name" placeholder="Enter Your Full Name" required>
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
                <span onclick="toggleAuthForms()" class="text-primary">
                    <button onclick="openRegisterModal('user')">Register as User</button>
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
                loadServiceDetails(serviceId);
            }
        <?php else: ?>
            openLoginModal();
        <?php endif; ?>
    }

    function loadServiceDetails(serviceId) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'service_details.php?id=' + serviceId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('content').innerHTML = xhr.responseText;
                window.scrollTo(0, 0);
            }
        };
        xhr.send();
    }

    function toggleAuthForms() {
        const registerPage = document.querySelector('.register-page');
        const loginPage = document.querySelector('.login-page');
        registerPage.style.display = registerPage.style.display === 'none' ? 'block' : 'none';
        loginPage.style.display = loginPage.style.display === 'none' ? 'block' : 'none';
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
        const input = document.getElementById('searchInput').value.toLowerCase();
        const services = document.querySelectorAll('.grid-item');

        services.forEach(service => {
            const nameElement = service.querySelector('h3');
            const name = nameElement.textContent.toLowerCase();
            if (name.includes(input)) {
                service.style.display = 'block';
                const regex = new RegExp(`(${input})`, 'gi');
                nameElement.innerHTML = name.replace(regex, '<span class="highlight">$1</span>');
            } else {
                service.style.display = 'none';
                nameElement.innerHTML = nameElement.textContent;
            }
        });
    }

    document.getElementById('searchInput').addEventListener('input', searchServices);
</script>
