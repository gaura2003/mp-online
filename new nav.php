<?php
session_start();
include './php/connection.php';

$user_role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$profile_image = null;

// Fetch user details from the database
if ($user_id && isset($username)) {
  $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();

  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $profile_image = $user['profile_picture'];
  }
  $stmt->close();
}

// Generate initials from username (if profile image is not set)
$nameParts = explode(' ', trim($username));
$initials = '';
foreach ($nameParts as $part) {
  $initials .= strtoupper(substr($part, 0, 1));
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
  <!-- #070b18 -->
  <div class="relative bg-[ #A0AEC0] h-full min-h-screen font-[sans-serif]">
    <div class="flex items-start bg-[#A0AEC0]">
      <nav id="sidebar" class="lg:w-[270px] max-lg:fixed transition-all duration-500 shrink-0 z-[100]">
        <div id="sidebar-collapse-menu"
          class="bg-[#081028] shadow-lg h-screen fixed top-0 left-0 overflow-auto overflow-x-hidden z-[99] lg:w-[270px] max-lg:w-0 max-lg:invisible transition-all duration-500">
          <div class="bg-[#081028] flex items-center gap-4 pt-6 pb-2 px-4 sticky top-0 min-h-[64px] z-[100]">
            <a class="flex items-center gap-2">
              <ion-icon name="logo-ionic" class="w-8 h-8 text-[#017bfe]"></ion-icon>
              <p class="text-base font-semibold text-gray-300 tracking-wide">Dashboard</p>
            </a>

            <button id="close-sidebar" class='ml-auto'>
              <ion-icon name="close" class="w-5 h-5 text-gray-300"></ion-icon>
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
                <a onclick="loadContent('main.php')"
                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="home" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Dashboard</span>
                </a>
              <li>
                <a onclick="loadContent('services.php')"
                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="add" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Services</span>

                </a>
              </li>

              <li>
                <a onclick="loadContent('order.php')"

                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="time" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Orders</span>

                </a>

              </li>

              <li>
                <a onclick="loadContent('help.php')"
                  class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="person" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Help</span>

                </a>
              </li>

              <li>
                <a class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                  <ion-icon name="add-circle" class="w-[18px] h-[18px] mr-3"></ion-icon>
                  <span class="overflow-hidden text-ellipsis whitespace-nowrap">Actions</span>
                  <ion-icon name="chevron-down" class="arrowIcon w-2.5 h-2.5 -rotate-90 ml-auto transition-all duration-500"></ion-icon>
                </a>
                <ul class="sub-menu max-h-0 overflow-hidden transition-[max-height] duration-500 ease-in-out ml-8">
                  <li>
                    <a onclick="loadContent('profile.php')"
                      class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                      <span>Profile</span>
                    </a>
                  </li>
                  <?php if ($user_role === 'worker'): ?>
                    <li>
                      <ion-icon name="laptop-outline" class="w-5 h-5 text-gray-300"></ion-icon>

                      <a onclick="loadContent('order.phpWorker?user_id=<?php echo $_SESSION['user_id']; ?>')"
                        class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                        <span>Go to Work</span>
                      </a>
                    </li>

                  <?php endif; ?>
                  <?php if ($user_role === 'admin'): ?>

                    <li>
                      <ion-icon name="person-outline" class="w-5 h-5 text-gray-300"></ion-icon>
                      <a onclick="loadContent('admin services')"
                        class="text-gray-300 text-sm block cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                        <span>Admin Panel</span>
                      </a>
                    </li>

                  <?php endif; ?>
                  <li>
                    <a onclick="loadContent('logout.php')" onclick="return confirm('Are you sure you want to log out?');"
                      class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2 transition-all duration-300">
                      <ion-icon name="log-out-outline" class="w-5 h-5 text-gray-300"></ion-icon>
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
                  <a onclick="loadContent('contact.php')"
                    class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                    <ion-icon name="shield" class="w-[18px] h-[18px] mr-3"></ion-icon>
                    <span>Contact Us</span>
                  </a>
                </li>
                <li>
                  <a onclick="loadContent('settings.php')"
                    class="text-gray-300 text-sm flex items-center cursor-pointer hover:bg-[#0b1739] rounded-md px-3 py-2.5 transition-all duration-300">
                    <ion-icon name="settings" class="w-[18px] h-[18px] mr-3"></ion-icon>
                    <span>Preferences</span>
                  </a>
                </li>
              </ul>
              <!-- Profile section -->
              <div class="mt-6 flex items-center cursor-pointer">
                <!-- If profile image exists, display it; otherwise, display initials inside a circle -->
                <?php if ($profile_image): ?>
                  <img src="uploads/<?php echo htmlspecialchars($profile_image); ?>" class="w-9 h-9 rounded-full border-2 border-gray-600 shrink-0" />
                <?php else: ?>
                  <div class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-600 text-white">
                    <span class="text-sm"><?php echo $initials; ?></span>
                  </div>
                <?php endif; ?>

                <div class="ml-4">
                  <p class="text-sm text-gray-300 whitespace-nowrap"><?php echo htmlspecialchars($username); ?></p>
                  <p class="text-xs text-gray-400 whitespace-nowrap">Active free account</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <button class='fixed top-0 left-0 bg-[#081028] z-50 sm:w-full md:w-[50px] lg:w-[50px] w-full'>
        <div class="flex flex-col gap-10 sm:flex p-[15px] h-full">
          <ion-icon id="open-sidebar" name="menu" class="w-5 h-5 text-gray-300"></ion-icon>
        </div>

        <!-- Other icons hidden by default on mobile, shown on larger screens -->
        <div class="flex flex-col gap-10  sm:hidden p-[15px] h-full lg:flex md:flex hidden">
          <ion-icon id="open-sidebar" name="search" class="w-5 h-5 text-gray-300"></ion-icon>
          <ion-icon onclick="loadContent('main.php')" name="home" class="w-5 h-5 text-gray-300"></ion-icon>
          <ion-icon onclick="loadContent('services.php')" name="add" class="w-5 h-5 text-gray-300"></ion-icon>
          <ion-icon onclick="loadContent('order.php')" name="time" class="w-5 h-5 text-gray-300"></ion-icon>
          <ion-icon onclick="loadContent('help.php')" name="shield" class="w-5 h-5 text-gray-300"></ion-icon>
          <ion-icon onclick="loadContent('profile.php')" name="person" class="w-5 h-5 text-gray-300"></ion-icon>
          <ion-icon onclick="loadContent('settings.php')" name="settings" class="w-5 h-5 text-gray-300"></ion-icon>
          <ion-icon onclick="loadContent('order.phpWorker?user_id=<?php echo $_SESSION['user_id']; ?>')" name="laptop-outline" class="w-5 h-8 text-gray-300"></ion-icon>
          <ion-icon onclick="loadContent('logout.php')" name="log-out-outline" class="w-5 h-5 text-gray-300"></ion-icon>
        </div>
      </button>


      <!-- Content Container -->
      <div class="w-full relative top-12 sm:top-48 md:top-0 lg-top-0 xl:top-0 2xl:top-0">
        <div id="content ">
          <?php include 'main.php'; ?>
        </div>
      </div>

      <?php include 'footer.php'; ?>
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
      sidebar.style.cssText = 'width: 50px;';
    });
  });
  // Load Content Dynamically
  function loadContent(page) {
    fetch(page)
      .then(response => {
        if (!response.ok) {
          throw new Error('Content could not be loaded');
        }
        return response.text();
      })
      .then(data => {
        document.getElementById('content').innerHTML = data;
      })
      .catch(error => {
        console.error('Error loading content:', error);
        document.getElementById('content').innerHTML = '<p>Error loading content.</p>';
      });
  }

  // // Load Content Dynamically
  // function loadContent(page) {
  //   const xhr = new XMLHttpRequest();
  //   xhr.open('GET', page, true);
  //   xhr.onreadystatechange = function() {
  //     if (xhr.readyState === 4 && xhr.status === 200) {
  //       document.getElementById('content').innerHTML = xhr.responseText;
  //     }
  //   };
  //   xhr.send();
  // }

  // // Initially load main.php content
  // document.addEventListener("DOMContentLoaded", () => {
  //   loadContent('main.php');
  // });
</script>

</html>