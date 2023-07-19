<?php
require_once '../classess/DatabaseHandler.php';
$db = new DatabaseHandler();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = $_POST; // Get all POST data

    // Sanitize and validate the input data if necessary

    $email = $postData['Email'];
    $phone = $postData['Phone'];

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
?>
