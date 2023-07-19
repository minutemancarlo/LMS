<?php
require_once '../classess/DatabaseHandler.php';

// Assuming the form data is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the email and password from the form data
    $email = $_POST['email'];
    $password = $_POST['password'];


    // Create an instance of the DatabaseHandler class
    $db = new DatabaseHandler();

    // Select the record from the database based on the email
    $result = $db->select("member", "*", "email = '$email'");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['Password'];

        // Verify the provided password against the stored password using password_verify()
        if (password_verify($password, $storedPassword)) {
            $isVerified = $row['is_verified'];

            if ($isVerified == 1) {
                $response = array(
                    'success' => true,
                    'message' => 'Login Successful'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Email Address not verified'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Invalid Credentials'
            );
        }
    } else {
        $response = array(
            'success' => false,
            'message' => 'Invalid Credentials'
        );
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>