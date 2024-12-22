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
                        //     echo '<span class="user-name" 
                        // id="user-name" 
                        // style="color:white; font-size:24px;  cursor:default; width:180px; padding-left:6px; " 
                        // data-full-name="' . htmlspecialchars($username, ENT_QUOTES) . '" 
                        // data-initials="' . htmlspecialchars($initials, ENT_QUOTES) . '">'
                        //         . $initials .
                        //         '</span>';
                        } else {
                            // echo '<ion-icon name="person-outline" class="nav__icon w-50 btn-success rounded-circle" style="font-size: 40px;"></ion-icon>';
                            // echo '<div>';
                            // echo '<button class="register-btn" onclick="openRegisterModal(\'user\')">Register</button>';
                            // echo '<button class="register-btn" onclick="openRegisterModal(\'worker\')">Register as worker</button>';
                            // echo '<button class="login-btn" onclick="openLoginModal()">Login</button>';
                            // echo '</div>';
                            
                        }
                        ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</head>
<body>
  <div class="relative bg-[#070b18] h-full min-h-screen font-[sans-serif]">
    <div class="flex items-start">
      <nav id="sidebar" class="lg:w-[270px] max-lg:fixed transition-all duration-500 shrink-0 z-[100]">
        <div id="sidebar-collapse-menu"
          class="bg-[#081028] shadow-lg h-screen fixed top-0 left-0 overflow-auto overflow-x-hidden z-[99] lg:w-[270px] max-lg:w-0 max-lg:invisible transition-all duration-500">
          <div class="bg-[#081028] flex items-center gap-4 pt-6 pb-2 px-4 sticky top-0 min-h-[64px] z-[100]">
            <a href="javascript:void(0)"  class="flex items-center gap-2">
              <ion-icon name="logo-ionic" class="w-8 h-8 text-[#017bfe]"></ion-icon>
              <p class="text-base font-semibold text-gray-300 tracking-wide">Dashboard</p>
            </a>

            <button id="close-sidebar" class='ml-auto'>
              <ion-icon name="menu" class="w-5 h-5 text-gray-300"></ion-icon>
            </button>
          </div>

          <div class="py-4 px-4">
            <div class="flex relative bg-[#0b1739] px-3 py-2.5 rounded-md border border-gray-600">
              <ion-icon name="search" class="w-4 mr-1 text-gray-400"></ion-icon>
              <input class="text-sm text-gray-300 outline-none bg-transparent px-1 max-w-[130px]"
                placeholder="Search..." />
            </div>
            <ul class="space-y-2 mt-6">
              <li>
                <a href="javascript:void(0)" data-page="main.php"
                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="home" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Dashboard</span>
                </a>
              <li>
                <a href="javascript:void(0)" data-page="services.php"
                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="add" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Services</span>
                  
                </a>
              </li>

              <li>
                <a href="javascript:void(0)" data-page="orders.php"

                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="time" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Orders</span>
                  
                </a>
              
              </li>

              <li>
                <a href="javascript:void(0)" data-page="help.php"
                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="person" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Help</span>
                  
                </a>
              </li>

              <li>
                <a href="javascript:void(0)"
                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="add-circle" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Actions</span>
                  <ion-icon name="chevron-down" class="arrowIcon w-2.5 h-2.5 -rotate-90 ml-auto transition-all duration-500"></ion-icon>
                </a>
                <ul class="sub-menu max-h-0 overflow-hidden transition-[max-height] duration-500 ease-in-out ml-8">
                  <li>
                    <a href="javascript:void(0)" data-page="profile.php"
                      class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                      <span>Profile</span>
                    </a>
                  </li>
                  <?php if ($user_role === 'worker'): ?>
                        <li>
                        <ion-icon name="laptop-outline" class="nav__icon"></ion-icon>

                    <a href="javascript:void(0)" data-page="Worker?user_id=<?php echo $_SESSION['user_id']; ?>" 
                      class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                      <span>Go to Work</span>
                    </a>
                  </li>
                        <li>
                  <ion-icon name="log-out-outline" class="nav__icon"></ion-icon>
                    <a href="javascript:void(0)" data-page="logout.php" onclick="return confirm('Are you sure you want to log out?');"
                      class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                      <span>Logout</span>
                    </a>
                  </li>
                    <?php endif; ?>
                    <?php if ($user_role === 'admin'): ?>
                       
                        <li>
                        <ion-icon name="person-outline" class="nav__icon"></ion-icon>
                    <a href="javascript:void(0)" data-page="admin services"
                      class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                      <span>Admin Panel</span>
                    </a>
                  </li>
                        <li>
                        <ion-icon name="log-out-outline" class="nav__icon"></ion-icon>
                    <a href="javascript:void(0)" data-page="logout.php" onclick="return confirm('Are you sure you want to log out?');"
                      class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                      <span>Logout</span>
                    </a>
                  </li>
                    <?php endif; ?>
                  <li>
                  <ion-icon name="log-out-outline" class="nav__icon"></ion-icon>
                    <a href="javascript:void(0)" data-page="logout.php" onclick="return confirm('Are you sure you want to log out?');"
                      class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                      <span>Logout</span>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>

            <hr class="border-gray-600 my-6" />

            <div>
              <ul class="space-y-2">
                <li>
                  <a href="javascript:void(0)" data-page="contact.php"
                    class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                    <ion-icon name="shield" class="w-[18px] h-[18px] mr-3"></ion-icon>
                    <span>Contact Us</span>
                  </a>
                </li>
                <li>
                  <a href="javascript:void(0)" data-page="settings.php"
                    class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                    <ion-icon name="settings" class="w-[18px] h-[18px] mr-3"></ion-icon>
                    <span>Preferences</span>
                  </a>
                </li>
              </ul>

              <div class="mt-6 flex items-center cursor-pointer">
                <img src='https://readymadeui.com/profile.webp'
                  class="w-9 h-9 rounded-full border-2 border-gray-600 shrink-0" />
                <div class="ml-4">
                  <p class="text-sm text-gray-300 whitespace-nowrap">John Doe</p>
                  <p class="text-xs text-gray-400 whitespace-nowrap">Active free account</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <button id="open-sidebar" class='ml-auto fixed top-[30px] left-[18px]'>
        <ion-icon name="menu" class="w-5 h-5 text-gray-300"></ion-icon>
      </button>

      <section class="main-content w-full p-6 max-lg:ml-8">
        <div>
          <div class="flex items-center flex-wrap gap-6">
            <div>
              <h3 class="text-lg font-semibold text-white">Welcome back, John</h3>
              <p class="text-xs text-gray-300">Streamlined dashboard layout featuring a welcoming header for user
                personalization.</p>
            </div>
            <div class="main-container">
            <?php include 'main.php'; ?>  

            </div>
          </div>
        </div>
        </section>
    </div>
  </div>
</body>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Select all parent menu items with submenus
    document.querySelectorAll('#sidebar ul > li > a').forEach((menu) => {
      menu.addEventListener('click', () => {
        const subMenu = menu.nextElementSibling;
        if (!subMenu) return;
        const arrowIcon = menu.querySelector('.arrowIcon');

        // Check if the submenu is currently open
        if (subMenu.classList.contains('max-h-0')) {
          subMenu.classList.remove('max-h-0');
          subMenu.classList.add('max-h-[500px]'); // Adjust height as needed
        } else {
          subMenu.classList.remove('max-h-[500px]');
          subMenu.classList.add('max-h-0');
        }

        // Toggle arrow rotation
        arrowIcon.classList.toggle('rotate-0');
        arrowIcon.classList.toggle('-rotate-90');
      });
    });

    let sidebarCloseBtn = document.getElementById('close-sidebar');
    let sidebarOpenBtn = document.getElementById('open-sidebar');
    let sidebarCollapseMenu = document.getElementById('sidebar-collapse-menu');
    let sidebar = document.getElementById('sidebar');

    sidebarOpenBtn.addEventListener('click', () => {
      sidebarCollapseMenu.style.cssText = 'width: 270px; visibility: visible; opacity: 1;';
      sidebar.style.cssText = 'width: 270px;';
    });

    sidebarCloseBtn.addEventListener('click', () => {
      sidebarCollapseMenu.style.cssText = 'width: 32px; visibility: hidden; opacity: 0;';
      sidebar.style.cssText = 'width: 32px;';
    });
  });
  
  document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('#sidebar a[data-page]');
    const contentArea = document.querySelector('.main-content');

    links.forEach(link => {
      link.addEventListener('click', () => {
        const page = link.getAttribute('data-page');

        // Fetch the content from the corresponding PHP file
        fetch(page)
          .then(response => {
            if (!response.ok) {
              throw new Error(`Error loading page: ${response.status}`);
            }
            return response.text();
          })
          .then(data => {
            // Inject the content into the main content area
            contentArea.innerHTML = data;
          })
          .catch(error => {
            console.error('Error:', error);
            contentArea.innerHTML = `<p class="text-red-500">Failed to load content. Please try again later.</p>`;
          });
      });
    });
  });
</script>


</html>