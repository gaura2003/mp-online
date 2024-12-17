<body id="body-pd">
    <div class="l-navbar" id="navbar">
        <nav class="nav">
            <div>
                <div class="nav__brand">
                    <ion-icon name="menu-outline" class="nav__toggle" id="nav-toggle"></ion-icon>
                    <a class="nav__logo">Service Station</a>
                </div>
                <div class="profile">
                    <div class="profile-pic"><ion-icon name="person-outline" class="nav__icon" style="font-size: 40px;"></ion-icon>
                    </div>
                    <div class="navbar-right">
                        <?php
                        session_start();
                        include './php/connection.php';
                        $user_role = $_SESSION['role'] ?? null;

                        if (isset($_SESSION['user_id'])) {

                            if (isset($_SESSION['username'])) {
                                // Split the full name into parts
                                $nameParts = explode(' ', trim($_SESSION['username']));
                                // Initialize an empty string for initials
                                $initials = '';

                                // Loop through the name parts and get the first letter of each
                                foreach ($nameParts as $part) {
                                    $initials .= strtoupper(substr($part, 0, 1));
                                }
                                // Display the initials
                                echo '<span class="user-name" style="color:white; font-size:18px;">' . $initials . '</span>';
                            }
                        } else {
                            echo '<button class="register-btn" onclick="openRegisterModal(\'user\')">Register</button>';
                            echo '<button class="register-btn" onclick="openRegisterModal(\'worker\')">Register as worker</button>';
                            echo '<button class="login-btn" id="takeServiceButton" onclick="openLoginModal()">Login</button>';
                        }
                        ?>
                    </div>
                </div>
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
                        <div class="nav__link ">
                            <ul class="collapse__menu">
                                <li class="menu-item">
                                    <div class="menu-icon"><img src="./images/admin-icon.svg" alt=""></div>
                                    <span>
                                        <a href="Worker?user_id=<?php $_SESSION['user_id'] ?>" style="text-decoration: none; color: white;">
                                            Go to Work
                                        </a>
                                    </span>
                                </li>
                            <?php endif; ?>
                            <?php if ($user_role === 'admin'): ?>
                                <li class="menu-item">
                                    <div class="menu-icon"><img src="./images/admin-icon.svg" alt=""></div>
                                    <span><a href="admin services" style="text-decoration: none; color: white;">Admin Panel</a></span>
                                </li>
                            <?php endif; ?>

                            <li class="menu-item">
                                <?php
                                if (isset($_SESSION['user_id'])) {
                                    echo '<a class="nav__link" href="logout.php" onclick="return confirm("Are you sure you want to log out?");">
    <ion-icon name="log-out-outline" class="nav__icon"></ion-icon>
    <span class="nav__name">Log Out</span>
</a>';

                                }
                                ?>
                            </li>

                            </ul>
                        </div>
                </div>
            </div>
        </nav>
    </div>
    <?php include 'main.php'; ?>

    <script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script>

    <script>
        const showMenu = (toggleId, navbarId, bodyId) => {
            const toggle = document.getElementById(toggleId),
                navbar = document.getElementById(navbarId),
                bodypadding = document.getElementById(bodyId)

            if (toggle && navbar) {
                toggle.addEventListener('click', () => {
                    navbar.classList.toggle('expander')
                    bodypadding.classList.toggle('body-pd')
                })
            }
        }
        showMenu('nav-toggle', 'navbar', 'body-pd')

        const linkColor = document.querySelectorAll('.nav__link')

        function colorLink() {
            linkColor.forEach(l => l.classList.remove('active'))
            this.classList.add('active')
        }
        linkColor.forEach(l => l.addEventListener('click', colorLink))

        const linkCollapse = document.getElementsByClassName('collapse__link')
        for (let i = 0; i < linkCollapse.length; i++) {
            linkCollapse[i].addEventListener('click', function() {
                const collapseMenu = this.nextElementSibling
                collapseMenu.classList.toggle('showCollapse')

                const rotate = collapseMenu.previousElementSibling
                rotate.classList.toggle('rotate')
            })
        }

        function loadContent(page) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', page, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('content').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
        $(document).ready(function() {
            $("#menu").on("click", function() {
                $("#menu").css("opacity", "0");
                $("#lgMenu").addClass("enter");
            });
            $("#exit").on("click", function() {
                $("#lgMenu").removeClass("enter");
                $("#menu").css("opacity", "1");
            });
        });
    </script>
</body>