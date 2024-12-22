<?php
include './php/connection.php';

$user_role = $_SESSION['role'] ?? null;
$is_logged_in = isset($_SESSION['user_id']); // Check if the user is logged in

// Initialize the services array and categories array
$services = [];
$categories = [];

// Check if there is a search query or category filter
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$category_id = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? intval($_GET['category_id']) : null;

try {
    // Fetch categories for the dropdown
    $category_stmt = $conn->prepare("SELECT * FROM service_categories");
    $category_stmt->execute();
    $category_result = $category_stmt->get_result();

    // Store categories in an array
    if ($category_result->num_rows > 0) {
        $categories = $category_result->fetch_all(MYSQLI_ASSOC);
    }

    // Prepare service query based on search query and category filter
    if (!empty($search_query) && $category_id) {
        $stmt = $conn->prepare("SELECT * FROM services WHERE category_id = ? AND service_name LIKE ?");
        $search_term = "%" . $search_query . "%";
        $stmt->bind_param("is", $category_id, $search_term);
    } elseif (!empty($search_query)) {
        $stmt = $conn->prepare("SELECT * FROM services WHERE service_name LIKE ?");
        $search_term = "%" . $search_query . "%";
        $stmt->bind_param("s", $search_term);
    } elseif ($category_id) {
        $stmt = $conn->prepare("SELECT * FROM services WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM services");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are services available
    if ($result->num_rows > 0) {
        $services = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();
    $category_stmt->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</style>
<div id="conten" class="h-auto">
    <div class="header">
        <h1>Welcome to Online Services</h1>
        <form method="GET" action="">
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
        <div class="left-content">
            <?php include('./Slider.php') ?>
        </div>
        <div class="right-content"></div>
    </div>
    <div class="services-header">
    <h1>Services</h1>
    <div class="filters">
        <!-- Dropdown for Category -->
        <select id="category-select" onchange="applyFilters()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $category_id ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Search Bar -->
        <input type="text" id="search-input" placeholder="Search services..." oninput="applyFilters()" />
    </div>
</div>

<div class="services">
    <?php if (!empty($services)): ?>
        <?php foreach ($services as $service): ?>
            <div class="service-card grid-item" data-service-id="<?php echo $service['id']; ?>" onclick="handleServiceClick(<?php echo $service['id']; ?>)">
                <img src="./admin services/<?php echo $service['image_url']; ?>" alt="<?php echo htmlspecialchars($service['service_name'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="card-content">
                    <h3 title="<?php echo htmlspecialchars($service['service_name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($service['service_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
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
        <div class="modal-content" style="width:50%; margin:5px auto;">
            <!-- Register Page -->
            <div class="register-page">

                <div class="bg p-5">
                    <span class="close" onclick="closeModal()">&times;</span>

                    <!-- Role Selection Buttons -->
                    <div class="role-selection" style="text-align:center; margin-bottom:20px;">
                        <button class="btn btn-primary" onclick="openRegisterModal('user')">Register as User</button>
                        <button class="btn btn-secondary" onclick="openRegisterModal('worker')">Register as Worker</button>
                    </div>

                    <h2 class="text-center text-white mb-4" id="registerHeading">Create Account as User</h2>
                    <p class="text-center text-white mb-4">Join WorkerHub to manage your work progress.</p>
                    <form method="POST" action="register.php">
                        <input type="hidden" id="role" name="role" value="user"> <!-- Role Hidden Input -->
                        <div class="grid-container">
                            <div class="form-group">
                                <i class="fas fa-user"></i>
                                <input type="text" name="fullname" class="form-control" placeholder="Enter your Full Name" required>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" class="form-control" placeholder="Your Email Address" required>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-phone"></i>
                                <input type="text" name="phone" class="form-control" placeholder="Enter Phone Number" required>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-home"></i>
                                <input type="text" name="address" class="form-control" placeholder="Your Home Address" required>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                            </div>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label text-white" for="terms">
                                I agree with <a href="#" class="text-primary">Terms & Conditions</a>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </form>
                    <p class="text-center mt-4 text-white">Already registered? <a href="#" onclick="toggleAuthForms()" class="text-primary">Log in</a></p>
                </div>
            </div>

            <!-- Login Page -->
            <div class="login-page  bg " style="display:none; height:580px;">
                
                <span class="close" onclick="closeModal()">&times;</span>
                <div class="bg d-flex flex-column justify-content-center align-items-center">
                <h2 class="text-center text-white">Login</h2>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <i class="fas fa-envelope"></i>
                        <input class="form-control" type="text" name="email" placeholder="Enter Email" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <input class="form-control" type="password" name="password" placeholder="Enter Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                <p class="text-center mt-4  text-white">Don't Have An Account? <span onclick="toggleAuthForms()" class="text-primary">Register Here</span></p>
                </div>
            </div>
        </div>
    </div>

    <script>

function applyFilters() {
        const category = document.getElementById('category-select').value;
        const searchQuery = document.getElementById('search-input').value.trim();
        const urlParams = new URLSearchParams();

        if (category) urlParams.set('category_id', category);
        if (searchQuery) urlParams.set('search', searchQuery);

        window.location.href = `?${urlParams.toString()}`;
    }
        function toggleAuthForms() {
            const registerPage = document.querySelector('.register-page');
            const loginPage = document.querySelector('.login-page');
            registerPage.style.display = registerPage.style.display === 'none' ? 'block' : 'none';
            loginPage.style.display = loginPage.style.display === 'none' ? 'block' : 'none';
        }

        function openRegisterModal(role) {
            document.getElementById('role').value = role.toLowerCase(); // Set role dynamically in the hidden input
            setRegisterHeading(role); // Set the heading dynamically
            document.getElementById('authModal').style.display = 'block';
            document.querySelector('.register-page').style.display = 'block';
            document.querySelector('.login-page').style.display = 'none';
        }

        function setRegisterHeading(role) {
            const heading = document.getElementById('registerHeading');
            heading.textContent = `Create Account as ${role}`;
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
    </script>


    <style>
        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .form-control {
            width: 100%;
            padding-left: 2.5rem;
            padding-right: 1rem;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            background-color: #1f2937;
            color: #d1d5db;
            border-radius: 0.375rem;
            outline: none;
        }

        .form-control:focus {
            background-color: #2d3748;
            color: rgb(230, 236, 245);
            border-color: #4299e1;
            box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .form-group {
            position: relative;
        }

        .form-group input {
            padding-left: 40px;
        }

        /* Change placeholder color */
        .form-control::placeholder {
            color: #a0aec0;
            /* Replace this with your desired color */
            opacity: 1;
            /* Ensures full visibility */
        }

        /* Optional: Adjust focus styles */
        .form-control:focus::placeholder {
            color: #4299e1;
            /* Placeholder color when input is focused */
        }

        .bg {
            background-color: black;
        }
    </style>