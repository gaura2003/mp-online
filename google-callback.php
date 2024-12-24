<?php
require_once 'vendor/autoload.php';

session_start();

// Initialize Google Client
$client = new Google_Client();
$client->setClientId('947214422416-p3126mh8f5koiq2bbfc7fpg831j89p7h.apps.googleusercontent.com');
$client->setClientSecret('AIzaSyAvc5AgrIdJa7uWJ--NCcACJGRMCsp3TXE');
$client->setRedirectUri('http://localhost/github%20clone/mp-online/google-callback.php');

$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);

// If the authorization code is returned from Google
if (isset($_GET['code'])) {
    try {
        // Fetch the access token using the authorization code
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        // Log token for debugging
        error_log(print_r($token, true));

        // If token retrieval fails, show error message
        if (isset($token['error'])) {
            echo 'Error fetching token: ' . $token['error'];
            exit();
        }

        // Set the access token for the client
        $client->setAccessToken($token);

        // Initialize the OAuth2 service
        $google_service = new Google_Service_Oauth2($client);

        // Fetch the user info
        $user_info = $google_service->userinfo->get();

        // Store user information in the session
        $_SESSION['email'] = $user_info->email;
        $_SESSION['name'] = $user_info->name;

        // Redirect to the home page (ensure the URL is correct)
        header('Location: http://localhost/github%20clone/mp-online/');
        exit();

    } catch (Exception $e) {
        // Catch any errors and display them
        echo 'Error: ' . $e->getMessage();
        exit();
    }
} else {
    // Handle error if no authorization code is returned
    echo 'Authorization code missing.';
    exit();
}
?>
