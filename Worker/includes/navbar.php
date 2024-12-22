<body id="body-pd">
    <div class="l-navbar" id="navbar">
        <nav class="nav">
            <div>
                <div class="nav__brand">
                    <ion-icon name="menu-outline" class="nav__toggle" id="nav-toggle"></ion-icon>
                    <a class="nav__logo">Service Station</a>
                </div>
                <div class="nav__list">
                    <a class="nav__link active" onclick="loadContent('work.php')">
                        <ion-icon name="home-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Dashboard</span>
                    </a>
                    <a class="nav__link" onclick="loadContent('profile.php')">
                        <ion-icon name="chatbubbles-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Profile</span>
                    </a>

                    <!-- Work History Collapse -->
                    <div class="nav__link" data-bs-toggle="collapse" data-bs-target="#collapseWorkHistory" aria-expanded="false" aria-controls="collapseWorkHistory">
                        <ion-icon name="folder-outline" class="nav__icon " id="nav-toggle"></ion-icon>
                        <span class="nav__name">Work History</span>
                        <ion-icon name="chevron-down-outline" class="collapse__link" class="nav__toggle" id="nav-toggle"></ion-icon>
                        <ul class="collapse" id="collapseWorkHistory">
                            <li><a class="collapse__sublink" onclick="loadContent('history.php?status=pending')">Pending</a></li>
                            <li><a class="collapse__sublink" onclick="loadContent('history.php?status=success')">Success</a></li>
                        </ul>
                    </div>


                    <a class="nav__link" onclick="loadContent('contact.php')">
                        <ion-icon name="people-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Contact Us</span>
                    </a>

                    <!-- Help Collapse -->
                    <div class="nav__link" data-bs-toggle="collapse" data-bs-target="#collapseHelp" aria-expanded="false" aria-controls="collapseHelp">
                        <ion-icon name="pie-chart-outline" class="nav__icon" class="collapse__link"></ion-icon>
                        <span class="nav__name">Help</span>
                        <ion-icon name="chevron-down-outline" class="collapse__link" class="nav__toggle" id="nav-toggle"></ion-icon>
                        <ul class="collapse pl-0" id="collapseHelp">
                            <li><a class="collapse__sublink" onclick="loadContent('help.php?topic=service')">Service Help</a></li>
                            <li><a class="collapse__sublink" onclick="loadContent('help.php?topic=payment')">Payment Problem</a></li>
                            <li><a class="collapse__sublink" onclick="loadContent('help.php?topic=other')">Other Problem</a></li>
                        </ul>
                    </div>

                    <a class="nav__link" onclick="loadContent('settings.php')">
                        <ion-icon name="settings-outline" class="nav__icon"></ion-icon>
                        <span class="nav__name">Settings</span>
                    </a>
                </div>
            </div>

            <a class="nav__link" href="../../logout.php" onclick="return confirm('Are you sure you want to log out?');">
                <ion-icon name="log-out-outline" class="nav__icon"></ion-icon>
                <span class="nav__name">Log Out</span>
            </a>
        </nav>
    </div>

    <div id="content">
        <?php include('./work.php'); ?>
    </div>

    <script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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

        // Load content dynamically
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
    </script>
</body>