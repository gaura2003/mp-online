<body id="body-pd">
    <!-- Navigation Sidebar -->
    <div class="l-navbar" id="navbar">
        <nav class="nav">
            <div>
                <div class="nav__brand">
                    <ion-icon name="menu-outline" class="nav__toggle" id="nav-toggle"></ion-icon>
                    <a class="nav__logo">Service Station</a>
                </div>
                <div class="profile">
                    <div class="profile-pic">
                        <ion-icon name="person-outline" class="nav__icon" style="font-size: 40px;"></ion-icon>
                    </div>
                    <div class="navbar-right">
                        <?php
                        session_start();
                        include './php/connection.php';
                        $user_role = $_SESSION['role'] ?? null;

                        if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
                            $nameParts = explode(' ', trim($_SESSION['username']));
                            $initials = '';
                            foreach ($nameParts as $part) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                            echo '<span class="user-name" style="color:white; font-size:18px;">' . $initials . '</span>';
                        } else {
                            echo '<button class="register-btn" onclick="openRegisterModal(\'user\')">Register</button>';
                            echo '<button class="register-btn" onclick="openRegisterModal(\'worker\')">Register as worker</button>';
                            echo '<button class="login-btn" onclick="openLoginModal()">Login</button>';
                        }
                        ?>
                    </div>
                </div>
                <!-- Navigation Links -->
                <div class="nav__list">
                    <a class="nav__link active" onclick="loadContent('main.php')">
                        <ion-icon name="home-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Dashboard</span>
                    </a>
                    <a class="nav__link" onclick="loadContent('profile.php')">
                        <ion-icon name="person-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Profile</span>
                    </a>
                    <a class="nav__link" onclick="loadContent('order.php')">
                        <ion-icon name="cart-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Order History</span>
                    </a>
                    <a class="nav__link" onclick="loadContent('contact.php')">
                        <ion-icon name="call-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Contact Us</span>
                    </a>
                    <a class="nav__link" onclick="loadContent('settings.php')">
                        <ion-icon name="settings-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Settings</span>
                    </a>
                    <?php if ($user_role === 'worker'): ?>
                        <a class="nav__link" href="Worker?user_id=<?php echo $_SESSION['user_id']; ?>">
                        <ion-icon name="laptop-outline" class="nav__icon"></ion-icon>
                            <span class="nav__name">Go to Work</span>
                        </a>
                        <a class="nav__link" href="logout.php" onclick="return confirm('Are you sure you want to log out?');">
                        <ion-icon name="log-out-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Log Out</span>
                    </a>
                    <?php endif; ?>
                    <?php if ($user_role === 'admin'): ?>
                        <a class="nav__link" href="admin services">
                        <ion-icon name="person-outline" class="nav__icon"></ion-icon>
                            <span class="nav__name">Admin Panel</span>
                        </a>
                        <a class="nav__link" href="logout.php" onclick="return confirm('Are you sure you want to log out?');">
                        <ion-icon name="log-out-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Log Out</span>
                    </a>
                    <?php endif; ?>
                    <a class="nav__link" href="logout.php" onclick="return confirm('Are you sure you want to log out?');">
                        <ion-icon name="log-out-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Log Out</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content Container -->
    <div id="content">
        <?php include 'main.php'; ?>
        
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script>
    <script>
        // Expand/Collapse Navbar
        const showMenu = (toggleId, navbarId, bodyId) => {
            const toggle = document.getElementById(toggleId),
                navbar = document.getElementById(navbarId),
                bodypadding = document.getElementById(bodyId);

            if (toggle && navbar) {
                toggle.addEventListener('click', () => {
                    navbar.classList.toggle('expander');
                    bodypadding.classList.toggle('body-pd');
                });
            }
        }
        showMenu('nav-toggle', 'navbar', 'body-pd');

        // Active Link Highlight
        const linkColor = document.querySelectorAll('.nav__link');
        function colorLink() {
            linkColor.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        }
        linkColor.forEach(l => l.addEventListener('click', colorLink));

        // Load Content Dynamically
        function loadContent(page) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', page, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('content').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Initially load main.php content
        document.addEventListener("DOMContentLoaded", () => {
            loadContent('main.php');
        });
    </script>
</body>
