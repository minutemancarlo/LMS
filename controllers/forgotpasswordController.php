<?php
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
$settings = new SystemSettings();
$baseURL = $settings->getBaseURL();

if (isset($_POST['email'])) {
    $email = $_POST['email'];


    $db = new DatabaseHandler();


    $result = $db->select('member', '*', "Email = '$email'");

    if ($result->num_rows === 1) {

        $row = $result->fetch_assoc();
        $name = $row['Name'];
        $email = $row['Email'];

        $memberID = $row['MemberID'];

        // Generate a random 8-character alphanumeric temporary password
        $temporaryPassword = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);

        // Encrypt the temporary password using password_hash
        $hashedPassword = password_hash($temporaryPassword, PASSWORD_DEFAULT);

        // Update the member table with the temporary password
        $data = array(
          'Password' => $hashedPassword
        );
        $where = "MemberID = '$memberID'";
        $db->update('member', $data, $where);


        // Define the URL endpoint
        $url = $baseURL.'email/send.php'; // Replace with the actual URL

        // Create an array with the POST data
        $data = array(
          'email' => $email,
          'subject' => "Password Reset",
          'link' => $temporaryPassword,
          'name' => ucwords($name),
          'action' => "resetpassword"
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
        $response = [
            'success' => true,
            'message' => 'Success! Please check your email.'
        ];

        echo json_encode($response);
    } else {

        $response = array(
            'success' => false,
            'message' => 'Email does not exist in the member table.'
        );
        echo json_encode($response);
    }
}
?>
