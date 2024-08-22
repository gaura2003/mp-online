<div class="sidebar">
            <div class="profile">
                <div class="profile-pic"><img src="./images/user-solid.svg" alt=""></div>
                <div class="navbar-right">
                    <?php
$user_role = $_SESSION['role'] ?? null;

if (isset($_SESSION['user_id'])) {
    echo '<span class="user-name" style="color:white; font-size:18px;">' . $_SESSION['username'] . '</span>';
} else {
    echo '<button class="register-btn" onclick="openRegisterModal(\'user\')">Register</button>';
    echo '<button class="register-btn" onclick="openRegisterModal(\'worker\')">Register as worker</button>';
    echo '<button class="login-btn" id="takeServiceButton" onclick="openLoginModal()">Login</button>';
}
?>
                </div>
            </div>
            <ul class="menu">
                <li class="menu-item">
                    <div class="menu-icon"><img src="./images/house-solid.svg" alt=""></div>
                    <span>Home</span>
                </li>
                <li class="menu-item">
                    <div class="menu-icon"><img src="./images/handshake-solid.svg" alt=""></div>
                    <span>Services</span>
                </li>
                <li class="menu-item">
                    <div class="menu-icon"><img src="./images/address-card-solid.svg" alt=""></div>
                    <span>About us</span>
                </li>
                <li class="menu-item">
                    <div class="menu-icon"><img src="./images/phone-solid.svg" alt=""></div>
                    <span>Contact us</span>
                </li>
                <li class="menu-item">
                    <div class="menu-icon"><img src="./images/gear-solid.svg" alt=""></div>
                    <span>Settings</span>
                </li>
                <?php if ($user_role === 'worker'): ?>
                <li class="menu-item">
                    <div class="menu-icon"><img src="./images/admin-icon.svg" alt=""></div>
                    <span>
    <a href="Worker?user_id=<?php $_SESSION['user_id'] ?>" style="text-decoration: none; color: white;">
        Go to Work
    </a>
</span>

                </li>
                <?php endif;?>
                <?php if ($user_role === 'admin'): ?>
                <li class="menu-item">
                    <div class="menu-icon"><img src="./images/admin-icon.svg" alt=""></div>
                    <span><a href="admin services" style="text-decoration: none; color: white;">Admin Panel</a></span>
                </li>
                <?php endif;?>

                <li class="menu-item">
                    <?php
if (isset($_SESSION['user_id'])) {
    echo '<div class="menu-icon"><img src="./images/right-to-bracket-solid.svg" alt=""></div>';
    echo '<a href="logout.php" style="text-decoration: none; color:white;" class="logout-btn">Logout</a>';
}
?>
                </li>
            </ul>
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
                            <p>Don't Have An Account? <span onclick="toggleAuthForms()" class="text-primary">Register Here</span></p>
                        </form>
                    </div>
                </div>
            </div>