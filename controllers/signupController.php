<?php
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
$settings = new SystemSettings();
$db = new DatabaseHandler();

$baseURL = $settings->getBaseURL();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = $_POST; // Get all POST data

    // Sanitize and validate the input data if necessary

    $email = $postData['Email'];
    $phone = $postData['Phone'];
    $name = $postData['Name'];
    $postData['Password'] = password_hash($postData['Password'], PASSWORD_DEFAULT);
    $postData['Verification_token'] = generateVerificationToken();
    $token=$postData['Verification_token'];

    // Check if the email or phone number is already registered
    $existingEmail = $db->select('member', 'Email', "Email = '$email'");
    $existingPhone = $db->select('member', 'Phone', "Phone = '$phone'");

    if ($existingEmail->num_rows > 0 || $existingPhone->num_rows > 0) {
        // Email or phone number is already registered
        $response = [
            'success' => false,
            'message' => 'Email or phone number already registered.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();

    } else {
        // Insert the data into the member table
        $insertResult = $db->insert('member', $postData);

        if ($insertResult === true) {


            // Define the URL endpoint
            $url = $baseURL.'email/send.php'; // Replace with the actual URL

            // Create an array with the POST data
            $data = array(
              'email' => $email,
              'subject' => "Email Verification ",
              'link' => $baseURL."auth/verify.php?q=".$token,
              'name' => ucwords($name),
              'action' => "register"
            );

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Execute cURL request and get the response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
              $error = curl_error($ch);
              // Handle the error as needed
            } else {
              // Process the response
              // $responseData = json_decode($response, true);
              // Access the response data using $responseData array
            }

            // Close cURL session
            curl_close($ch);
            // Successful insert
            $response = [
                'success' => true,
                'message' => 'Success! Please check your email for verification.'
            ];
        } else {
            // Insert failed
            $response = [
                'success' => false,
                'message' => 'Unexpected error occurred.'
            ];
        }
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);


}

function generateVerificationToken() {
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$tokenLength = 32;
$token = '';

$maxCharIndex = strlen($characters) - 1;

// Generate random characters to form the token
for ($i = 0; $i < $tokenLength; $i++) {
    $token .= $characters[rand(0, $maxCharIndex)];
}

return $token;
}
?>
