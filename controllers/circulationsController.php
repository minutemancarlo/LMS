<?php
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';
require_once '../classess/DatabaseHandler.php';

$settings = new SystemSettings();
$session = new CustomSessionHandler();
$settings->setDefaultTimezone();
// Initialize the DatabaseHandler
$db = new DatabaseHandler();

// Check if the action is set and is equal to "select"
if (isset($_POST['action']) && $_POST['action'] === "select") {
    $query = "SELECT * FROM loan a
              INNER JOIN member b ON a.MemberID = b.MemberID";

    // Execute the query using the DatabaseHandler
    $result = $db->executeQuery($query);

    // Check if the query execution was successful
    if ($result) {
        // Fetch all the rows from the result as an associative array
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Close the result set
        $result->close();

        // Return the data as JSON response
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        // Return an error JSON response if the query execution failed
        $response = array('success' => false, 'message' => 'Error executing query.');
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
?>