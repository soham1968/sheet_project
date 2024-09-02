<?php
// Include the Composer autoloader
require 'vendor/autoload.php';

// Use the Google Client class
use Google\Client;

$client = new Client();
echo 'Google API Client loaded successfully.';
