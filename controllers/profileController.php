<?php
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';
$settings=new SystemSettings();
$db = new DatabaseHandler();


if (isset($_POST['action']) && $_POST['action'] === 'update') {
    // Prepare the update data
    $updateData = array(
        'Profile' => $_POST['profilePicture'],
        'Name' => $_POST['fullName'],
        'Phone' => $_POST['phoneNumber'],
        'Email' => $_POST['email'],
        'Password' => password_hash($postData['password'], PASSWORD_DEFAULT)       
    );

    // Get memberID from your authentication system or session
    $memberID = $_SESSION['memberID']; // Replace this with your actual session data

    // Create a DatabaseHandler instance    

    // Perform the update
    $where = "memberID = " . $db->escape($memberID);
    $updateResult = $db->update('member', $updateData, $where);

    if ($updateResult) {
        $response = array(
            'success' => true,
            'message' => 'Profile updated successfully.'
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Failed to update profile.'
        );
    }

    // Return the JSON response
    echo json_encode($response);
}

?>