<?php
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';
$settings=new SystemSettings();
$db = new DatabaseHandler();
$session = new CustomSessionHandler();


if (isset($_POST['action']) && $_POST['action'] === 'update') {

    if (isset($_POST['Password'])) {
      $_POST['Password']=password_hash($_POST['Password'], PASSWORD_DEFAULT);
    }
    unset($_POST['action']);

    // Get memberID from your authentication system or session
    $memberID = $session->getSessionVariable('Id');

    // Create a DatabaseHandler instance

    // Perform the update
    $where = "memberID = " . $db->escape($memberID);
    $updateResult = $db->update('member', $_POST, $where);

    if ($updateResult) {
      $session->setSessionVariable("Name",$_POST['Name']);
      $session->setSessionVariable("phone",$_POST['Phone']);
      $session->setSessionVariable("address",$_POST['Address']);
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
