<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

// Setup Google Client
$client = new Client();
$client->setApplicationName('Google Sheets API PHP');
$client->setScopes(Sheets::SPREADSHEETS_READONLY);
$client->setAuthConfig('C:\xampp\htdocs\sheet_project\cred.json'); // path to your JSON credentials file
$client->setAccessType('offline');

// Initialize Sheets Service
$service = new Sheets($client);
$spreadsheetId = '1h4Una9y3EJwkOxoiAjlUEUKy2YHL2XlXkjM1tiden-A'; // Replace with your spreadsheet ID
$range = 'Sheet1!A1:C6'; // Adjust the range according to your sheet

// Fetch data from the sheet
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// Check if data is retrieved
if (empty($values)) {
    echo "No data found.";
} else {
    foreach ($values as $row) {
        echo implode(", ", $row) . "<br>";
    }
}
