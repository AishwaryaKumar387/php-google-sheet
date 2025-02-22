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
require 'vendor/autoload.php';  // Ensure this is the correct path for your project

// Step 1: Set up Google Client and authenticate with the credentials file
$client = new Google_Client();
$client->setApplicationName('Google Sheets API PHP');
$client->setScopes([
    Google_Service_Sheets::SPREADSHEETS,
    Google_Service_Drive::DRIVE  // Add Google Drive API scope to manage file permissions
]);
$client->setAuthConfig('google-sheets-credentials.json');  // Path to your Google Sheets API credentials JSON file
$client->setAccessType('offline');

// Step 2: Initialize Google Sheets service
$service = new Google_Service_Sheets($client);

// Step 3: Set the Spreadsheet ID
$spreadsheetId = '1dg7dHMwh4C7bw5qPw_CG83KFvZefChB4miQNOWhODCs';  // Your spreadsheet ID

// Step 4: Clear all rows in the sheet
$clearRange = 'Sheet1!A2:D';  // Specify the range to clear, starting from A2 downwards (so it won't clear the header row)
$clearRequest = new Google_Service_Sheets_ClearValuesRequest();

$service->spreadsheets_values->clear($spreadsheetId, $clearRange, $clearRequest);

// Step 5: Define the new user data you want to add (replace this with real data or form input)
$newUsers = [
    [
        'user_id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'phone' => '123-456-7890'
    ],
    [
        'user_id' => 2,
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'phone' => '987-654-3210'
    ]
];

// Step 6: Prepare the rows to be added
$newRows = [];
foreach ($newUsers as $user) {
    $newRows[] = [
        (string)$user['user_id'],  // User ID
        (string)$user['name'],     // Name
        (string)$user['email'],    // Email
        (string)$user['phone']     // Phone
    ];
}

// Step 7: Prepare the request body to add the new rows
$requestBody = new Google_Service_Sheets_ValueRange([
    'values' => $newRows
]);

$params = [
    'valueInputOption' => 'USER_ENTERED',  // 'USER_ENTERED' allows the spreadsheet to interpret the data as if entered by the user
];

// Step 8: Add the new rows to the spreadsheet
$response = $service->spreadsheets_values->append(
    $spreadsheetId,
    'Sheet1!A2',  // Specify to start appending from A2 (keeping the header intact)
    $requestBody,
    $params
);

// Step 9: Construct the URL of the updated spreadsheet
$spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/' . $spreadsheetId . '/edit';

// Step 10: Output the result
echo 'Spreadsheet updated! You can view it here: <a href="' . $spreadsheetUrl . '" target="_blank">' . $spreadsheetUrl . '</a>';
?>
