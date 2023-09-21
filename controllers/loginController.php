<?php
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';
$settings = new SystemSettings();
$session = new CustomSessionHandler();
// Assuming the form data is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the email and password from the form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $db = new DatabaseHandler();
    $result = $db->select("member", "*", "email = '$email'");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['Password'];
        $role = $row['Role'];
        $name = ucwords($row['Name']);
        $email = $row['Email'];
        $phone = $row['Phone'];
        $ID = $row['MemberID'];
        $profid = $row['id'];
        $address= $row['Address'];
        // Verify the provided password against the stored password using password_verify()
        if (password_verify($password, $storedPassword)) {
            $isVerified = $row['is_verified'];
            $isActive = $row['is_active'];

            if ($isVerified == 1 && $isActive == 1) {
                $response = array(
                    'success' => true,
                    'message' => 'Login Successful'
                );
                $logMessage="User ".$email." login successful.";
                $settings->createLogFile("LoginController", $logMessage);
                $session->setSessionVariable("Role",$role);
                $session->setSessionVariable("Name",$name);
                $session->setSessionVariable("Id",$ID);
                $session->setSessionVariable("profId",$profid);
                $session->setSessionVariable("email",$email);
                $session->setSessionVariable("phone",$phone);
                $session->setSessionVariable("address",$address);

            } else {
              if ($isActive == 0) {
                $response = array(
                    'success' => false,
                    'message' => 'Your account is temporarily deactivated'
                );
              }else{
                $response = array(
                    'success' => false,
                    'message' => 'Email Address not verified'
                );
              }
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
