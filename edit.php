<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debugging function to print and stop execution
function dd($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die();
}

// Include the Composer autoloader to load the required Google API classes
require 'vendor/autoload.php';  // Ensure this is correct path for your project

// Step 1: Set up Google Client and authenticate with the credentials file
$client = new Google_Client();
$client->setApplicationName('Google Sheets API PHP');
$client->setScopes([
    Google_Service_Sheets::SPREADSHEETS,
    Google_Service_Drive::DRIVE  // Add Google Drive API scope to manage file permissions
]);
$client->setAuthConfig('google-sheets-credentials.json');  // Path to your Google Sheets API credentials JSON file
$client->setAccessType('offline');

// Step 2: Initialize Google Sheets and Google Drive services
$service = new Google_Service_Sheets($client);

// Step 3: Set the Spreadsheet ID
$spreadsheetId = '1dg7dHMwh4C7bw5qPw_CG83KFvZefChB4miQNOWhODCs';  // Your spreadsheet ID

// Step 4: Define the new user data you want to add (replace this with real data or form input)
$newUserData = [
    'user_id' =>4,
    'name' => 'Alice Johnson',
    'email' => 'alice.johnson@example.com',
    'phone' => '555-123-4567'
];

// Step 5: Prepare the row data for appending
// Directly use the array of values without userEnteredValue structure
$newRow = [
    [
        (string)$newUserData['user_id'],  // User ID
        (string)$newUserData['name'],     // Name
        (string)$newUserData['email'],    // Email
        (string)$newUserData['phone']     // Phone
    ]
];

// Step 6: Append the new row to the spreadsheet
$requestBody = new Google_Service_Sheets_ValueRange([
    'values' => $newRow
]);

$params = [
    'valueInputOption' => 'USER_ENTERED',  // 'USER_ENTERED' allows the spreadsheet to interpret the data as if entered by the user
];

// Append the new row to the existing sheet (Sheet1)
$response = $service->spreadsheets_values->append(
    $spreadsheetId,
    'Sheet1!A1',  // The range to start appending, using 'A1' means it will append below the last row
    $requestBody,
    $params
);

// Step 7: Construct the URL of the updated spreadsheet
$spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/' . $spreadsheetId . '/edit';

// Step 8: Return the URL
echo 'Spreadsheet updated! You can view it here: <a href="' . $spreadsheetUrl . '" target="_blank">' . $spreadsheetUrl . '</a>';
?>
