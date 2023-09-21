<?php
// Include the necessary files and initialize the database connection
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';
$settings=new SystemSettings();
$db = new DatabaseHandler();

// Check if the action POST variable is set
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'select') {
        // Select data from the member table using DatabaseHandler select method
        $result = $db->select('member');

        // Check if the query was successful
        if ($result) {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            // Return the data as JSON
            echo json_encode($data);
        } else {
            // Return an error message if the query fails
            echo json_encode(array('error' => 'Unable to fetch data from the database.'));
        }
    }

    if ($action === "update") {
            // Get the data from the POST variables
            $memberID = $_POST["memberID"];
            $memberName = $_POST["memberName"];
            $memberEmail = $_POST["memberEmail"];
            $memberPhone = $_POST["memberPhone"];
            $memberAddress = $_POST["memberAddress"];
            $role = $_POST["memberRole"];
            $isActive = $_POST["is_active"];
            $session=new CustomSessionHandler();

            if ($memberID==$session->getSessionVariable('Id')) {
              $response = array("success" => false, "message" => "You cannot modify role on your own account.");
              echo json_encode($response);
              exit();
            }

            // Create a new instance of the DatabaseHandler class
            $db = new DatabaseHandler();

            // Prepare the data for update
            $data = array(
                "Name" => $memberName,
                "Email" => $memberEmail,
                "Phone" => $memberPhone,
                "Address" => $memberAddress,
                "Role" => $role,
                "is_active" => $isActive
            );

            // Create the WHERE clause for the update query
            $where = "MemberID = '$memberID'";

            // Perform the update operation
            $updateResult = $db->update("member", $data, $where);

            // Check if the update was successful
            if ($updateResult) {
                $role=$role=='0'?'Administrator':'Standard User';
                $logMessage = "Member Information: Role Update to ".$role.", Updated by ".$_POST['by'];
                $settings->createLogFile("memberController", $logMessage);
                $response = array("success" => true, "message" => "Member updated successfully.");
            } else {
                $response = array("success" => false, "message" => "Failed to update member.");
            }

            // Send the JSON response
            echo json_encode($response);

    }
} else {
    // Return an error message if the action POST variable is missing
    echo json_encode(array('error' => 'Action parameter is missing.'));
}
?>
