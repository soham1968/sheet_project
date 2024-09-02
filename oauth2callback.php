<?php
require 'vendor/autoload.php';

use Google\Client;

// Start the session
session_start();

// Set up Google Client
$client = new Client();
$client->setAuthConfig('C:\xampp\htdocs\sheet_project\cred.json'); // Update with your client_secret file path
$client->setRedirectUri('http://localhost/sheet_project/oauth2callback.php'); // Update with your redirect URI

// Exchange authorization code for access token
if (isset($_GET['code'])) {
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: http://localhost/sheet_project/index.php'); // Redirect to main script
    exit();
} else {
    echo 'Authorization code not found.';
}
