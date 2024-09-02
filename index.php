<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Set up Google Client
$client = new Client();
$client->setApplicationName('Google Sheets API PHP');
$client->setScopes(Sheets::SPREADSHEETS_READONLY);
$client->setAuthConfig('C:\xampp\htdocs\sheet_project\cred.json'); // Update with your client_secret file path
$client->setRedirectUri('http://localhost/sheet_project/oauth2callback.php'); // Update with your redirect URI
$client->setAccessType('offline');

// Check if an OAuth code is present
if (isset($_GET['code'])) {
    // Exchange authorization code for access token
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: ' . filter_var($client->getRedirectUri(), FILTER_SANITIZE_URL));
    exit();
}

// Check if we have an access token stored
if (!isset($_SESSION['access_token'])) {
    // Generate an authentication URL
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit();
}

// Set the access token
$client->setAccessToken($_SESSION['access_token']);

// Initialize Sheets Service
$service = new Sheets($client);

// Specify the spreadsheet ID and range
$spreadsheetId = '1h4Una9y3EJwkOxoiAjlUEUKy2YHL2XlXkjM1tiden-A'; // Replace with your spreadsheet ID
$range = 'Sheet1!A1:C6'; // Adjust the range according to your sheet

// Connect to MySQL
$con = mysqli_connect('localhost', 'root', '', 'sheet');
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

try {
    // Fetch data from the sheet
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (is_null($values) || empty($values)) {
        echo "No data found.";
    } else {
        // Clear existing data
        $con->query("DELETE FROM sheet_table");

        // Insert new data
        $stmt = $con->prepare("INSERT INTO sheet_table (f_name, l_name, mail) VALUES (?, ?, ?)");

        // Skip the first row (header)
        $header = array_shift($values);

        foreach ($values as $row) {
            // Ensure the row has the expected number of columns
            if (count($row) == 3) {
                $stmt->bind_param("sss", $row[0], $row[1], $row[2]);
                $stmt->execute();
            }
        }

        $stmt->close();
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sheets Data</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Google Sheets Data</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>E-Mail</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Reconnect to MySQL
            $con = mysqli_connect('localhost', 'root', '', 'sheet');
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
                exit();
            }

            $result = $con->query("SELECT * FROM sheet_table");

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['f_name'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlspecialchars($row['l_name'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlspecialchars($row['mail'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "</tr>";
            }

            mysqli_close($con);
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
