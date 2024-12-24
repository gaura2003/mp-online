<body id="body-pd">
    
    <!-- Navigation Sidebar -->
    <div class="l-navbar " id="navbar" >
        <nav class="nav">
            <div>
                <div class="nav__brand" style="padding-right: 40px;">
                    <ion-icon name="menu-outline" class="nav__toggle" id="nav-toggle"></ion-icon>
                    <a class="nav__logo">Service Station</a>
                </div>
                <div class="profile">
                    <div class="profile-pic d-flex justify-content-center align-items-center rounded-circle" id="profile-pic">
                        <?php
                        session_start();
                        include './php/connection.php';

                        $user_role = $_SESSION['role'] ?? null;

                        if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
                            $username = $_SESSION['username'];
                            $nameParts = explode(' ', trim($username));
                            $initials = '';
                            foreach ($nameParts as $part) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                            echo '<span class="user-name" 
                        id="user-name" 
                        style="color:white; font-size:24px;  cursor:default; width:180px; padding-left:6px; " 
                        data-full-name="' . htmlspecialchars($username, ENT_QUOTES) . '" 
                        data-initials="' . htmlspecialchars($initials, ENT_QUOTES) . '">'
                                . $initials .
                                '</span>';
                        } else {
                            echo '<ion-icon name="person-outline" class="nav__icon w-50 btn-success rounded-circle" style="font-size: 40px;"></ion-icon>';
                            echo '<div>';
                            echo '<button class="register-btn" onclick="openRegisterModal(\'user\')">Register</button>';
                            echo '<button class="register-btn" onclick="openRegisterModal(\'worker\')">Register as worker</button>';
                            echo '<button class="login-btn" onclick="openLoginModal()">Login</button>';
                            echo '</div>';
                            
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
    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script type="module" src="https://unpkg.com/ionicons@5.1.2/dist/ionicons/ionicons.esm.js"></script>
    <script>
        // Expand/Collapse Navbar
        const showMenu = (toggleId, navbarId, bodyId) => {
            const toggle = document.getElementById(toggleId),
                navbar = document.getElementById(navbarId),
                bodypadding = document.getElementById(bodyId),
                userNameSpan = document.getElementById("user-name"),
                profile_pic = document.getElementById("profile-pic");

                profile_pic.style.width = "40px";
                        profile_pic.style.height = "40px";
                        profile_pic.style.backgroundColor = "green";
            if (toggle && navbar) {
                toggle.addEventListener('click', () => {
                    navbar.classList.toggle('expander');
                    if (navbar.classList.contains("expander")) {
                        userNameSpan.textContent = userNameSpan.getAttribute("data-full-name");
                        console.log(userNameSpan.getAttribute("data-full-name").length);
                        userNameSpan.style.fontSize = "1.2rem";
                        profile_pic.style.width = "180px";
                        profile_pic.style.borderRadius = "none";
                        profile_pic.style.backgroundColor = "transparent";
                    } else {
                        userNameSpan.textContent = userNameSpan.getAttribute("data-initials");
                        userNameSpan.style.fontSize = "1.3rem";
                        profile_pic.style.width = "40px";
                        profile_pic.style.height = "40px";
                        profile_pic.style.backgroundColor = "green";


                    }
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
