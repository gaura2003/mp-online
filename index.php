<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="./css/new.css">
    <link rel="stylesheet" href="./css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Scroll to Top Button */
        .scroll-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
            background-color: #6B46C1;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .scroll-top i {
            font-size: 20px;
        }

        .scroll-top:hover {
            background-color:rgb(243, 126, 151);
        }
    </style>
</head>
<body>
    <?php include './nav.php'; ?>

    <!-- Scroll to Top Button -->
    <div class="scroll-top" id="scroll-top">
    <ion-icon name="chevron-up-outline"></ion-icon>
    </div>
    <?php include 'footer.php'; ?>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Show/Hide Scroll-to-Top Button
        const scrollTopButton = document.getElementById('scroll-top');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTopButton.style.display = 'flex'; // Show button
            } else {
                scrollTopButton.style.display = 'none'; // Hide button
            }
        });

        // Scroll to Top Action
        scrollTopButton.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
