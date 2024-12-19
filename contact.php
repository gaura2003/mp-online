<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <!-- Meta Tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name='description' content="Contact Us">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Title -->
    <title>ServiceTop-Contact Us</title>

    <style>
        /* Ensure the form container is above other elements */
        .contauct-style1 {
            position: relative;
            z-index: 10; /* Ensure the form is above other elements */
        }

        /* Ensure the input fields are not covered by other elements */
        input, textarea, select {
            position: relative;
            z-index: 1;
        }

        /* Ensure the banner area doesn't overlap the form */
        .banner-area {
            position: relative;
            z-index: 0; /* Lower z-index to prevent overlap */
        }

        /* Enable pointer events on all elements */
        * {
            pointer-events: auto;
        }
    </style>
</head>

<body>

    <!--Banner Start-->
    <div class="banner-area flex"
        style="background-image:url(https://demo.websolutionus.com/servicetop/public/backend/images/breadcrumb/613a643191f3b1631216689.jpg);">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="banner-text">
                        <h1>Contact Us</h1>
                        <ul>
                            <li><a href="/">Home</a></li>
                            <li><span>Contact Us</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Banner End-->

    <!--Form Start-->
    <div class="contauct-style1 pt_50 pb_65">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <div class="about1-text mt_30">
                        <h1>Get In Touch</h1>
                        <p class="mb_30">
                            Lorem ipsum dolor sit amet, an labores explicari qui, eu nostrum copiosae argumentum
                            has. Latine propriae quo no, unum ridens expetenda id sit, at usu eius eligendi
                            singulis.
                        </p>
                    </div>
                    <form method="post" action="send-email.php">
                        <input type="hidden" name="_token" value="3Mhfzgm6E84GVWSf3iri88tLZxuNmLEeq8oWuJz7"
                            autocomplete="off">
                        <div class="row contact-form">
                            <div class="col-lg-6 form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" class="form-control" name="name" required>
                            </div>
                            <div class="col-lg-6 form-group">
                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" id="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-lg-6 form-group">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="tel" id="phone" class="form-control" name="phone">
                            </div>
                            <div class="col-lg-6 form-group">
                                <label for="subject">Subject <span class="text-danger">*</span></label>
                                <input type="text" id="subject" class="form-control" name="subject">
                            </div>
                            <div class="col-lg-12 form-group">
                                <label for="message">Message <span class="text-danger">*</span></label>
                                <textarea name="message" id="message" class="form-control"></textarea>
                            </div>

                            <div class="mt-3 mb-3 form-group">
                                <button type="submit" id="recaptcha" class="btn">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-5 gap-10">
                    <div class="contact-info-item bg1 mb-3 mt-3">
                        <div class="contact-info">
                            <span>
                            <ion-icon name="call-outline" class="nav__icon"></ion-icon>
                            Phone:
                            </span>
                            <div class="contact-text">
                                <p>
                                    123456789
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="contact-info-item bg2 mb-3">
                        <div class="contact-info">
                            <span>
                            <ion-icon name="mail-outline"></ion-icon> Email:
                            </span>
                            <div class="contact-text">
                                <p>
                                    test@test.com
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="contact-info-item bg3 mb-3">
                        <div class="contact-info">
                            <span>
                            <ion-icon name="location-outline"></ion-icon> Address:
                            </span>
                            <div class="contact-text">
                                <p>
                                    house-4, road-5, Sydney
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Form End-->

</body>

</html>
