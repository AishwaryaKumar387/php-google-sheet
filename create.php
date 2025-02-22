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
require 'vendor/autoload.php';  // Make sure the path is correct for your project

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
$driveService = new Google_Service_Drive($client);  // Initialize the Google Drive service to modify file permissions

// Example user data (you can replace this with real data from your database or any source)
$users = [
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

// Step 3: Define the header row once, it will be used for the entire sheet
$headerRow = [
    'values' => [
        ['userEnteredValue' => ['stringValue' => 'User ID']],
        ['userEnteredValue' => ['stringValue' => 'Name']],
        ['userEnteredValue' => ['stringValue' => 'Email']],
        ['userEnteredValue' => ['stringValue' => 'Phone']]
    ]
];

// Define the header row with formatting (Bold, Background Color, Center Alignment, Text Wrapping)
// $headerRow = [
//     'values' => [
//         ['userEnteredValue' => ['stringValue' => 'User ID'], 
//          'userEnteredFormat' => [
//              'textFormat' => ['bold' => true], 
//              'backgroundColor' => ['red' => 0.2, 'green' => 0.6, 'blue' => 1],
//              'horizontalAlignment' => 'CENTER',
//              'wrapStrategy' => 'WRAP'
//          ]
//         ],
//         ['userEnteredValue' => ['stringValue' => 'Name'], 
//          'userEnteredFormat' => [
//              'textFormat' => ['bold' => true], 
//              'backgroundColor' => ['red' => 0.2, 'green' => 0.6, 'blue' => 1],
//              'horizontalAlignment' => 'CENTER',
//              'wrapStrategy' => 'WRAP'
//          ]
//         ],
//         ['userEnteredValue' => ['stringValue' => 'Email'], 
//          'userEnteredFormat' => [
//              'textFormat' => ['bold' => true], 
//              'backgroundColor' => ['red' => 0.2, 'green' => 0.6, 'blue' => 1],
//              'horizontalAlignment' => 'CENTER',
//              'wrapStrategy' => 'WRAP'
//          ]
//         ],
//         ['userEnteredValue' => ['stringValue' => 'Phone'], 
//          'userEnteredFormat' => [
//              'textFormat' => ['bold' => true], 
//              'backgroundColor' => ['red' => 0.2, 'green' => 0.6, 'blue' => 1],
//              'horizontalAlignment' => 'CENTER',
//              'wrapStrategy' => 'WRAP'
//          ]
//         ]
//     ]
// ];

// Step 4: Prepare user data rows
$userRows = [];
foreach ($users as $user) {
    $userRows[] = [
        'values' => [
            ['userEnteredValue' => ['stringValue' => (string)$user['user_id']]],
            ['userEnteredValue' => ['stringValue' => (string)$user['name']]],
            ['userEnteredValue' => ['stringValue' => (string)$user['email']]],
            ['userEnteredValue' => ['stringValue' => (string)$user['phone']]]
        ]
    ];
}


// To add format:: Prepare user data rows
// $userRows = [];
// foreach ($users as $user) {
//     $userRows[] = [
//         'values' => [
//             ['userEnteredValue' => ['stringValue' => (string)$user['user_id']], 
//              'userEnteredFormat' => ['horizontalAlignment' => 'CENTER', 'wrapStrategy' => 'WRAP']],
//             ['userEnteredValue' => ['stringValue' => (string)$user['name']], 
//              'userEnteredFormat' => ['horizontalAlignment' => 'CENTER', 'wrapStrategy' => 'WRAP']],
//             ['userEnteredValue' => ['stringValue' => (string)$user['email']], 
//              'userEnteredFormat' => ['horizontalAlignment' => 'CENTER', 'wrapStrategy' => 'WRAP']],
//             ['userEnteredValue' => ['stringValue' => (string)$user['phone']], 
//              'userEnteredFormat' => ['horizontalAlignment' => 'CENTER', 'wrapStrategy' => 'WRAP']]
//         ]
//     ];
// }

// Step 5: Create a single spreadsheet with all users' data
$spreadsheet = new Google_Service_Sheets_Spreadsheet([
    'properties' => [
        'title' => 'User Data'  // Customize the title for the entire sheet
    ],
    'sheets' => [
        [
            'properties' => [
                'title' => 'Sheet1'  // Default sheet name
            ],
            'data' => [
                [
                    'rowData' => array_merge([$headerRow], $userRows)  // Merge the header row with all user data rows
                ]
            ]
        ]
    ]
]);

// Step 6: Create the spreadsheet
$response = $service->spreadsheets->create($spreadsheet);
$spreadsheetId = $response->spreadsheetId;  // Get the spreadsheet ID

// Step 7: Make the spreadsheet public by updating its permissions
$permission = new Google_Service_Drive_Permission();
$permission->setType('anyone');  // Allow anyone to access the spreadsheet
$permission->setRole('reader');  // Set the role as 'reader' for view-only access

// Apply the permission to the spreadsheet
$driveService->permissions->create($spreadsheetId, $permission);

// Step 8: Construct the URL to the spreadsheet
$spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/' . $spreadsheetId . '/edit';

// Step 9: Output the URL of the created spreadsheet
dd($spreadsheetUrl);  // Print and stop execution; this will display the spreadsheet URL
?>
