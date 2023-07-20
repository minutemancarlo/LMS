<?php
// Include the necessary files and initialize the database connection
require_once '../classess/DatabaseHandler.php';
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
    } else {
        // Return an error message if the action is not recognized
        echo json_encode(array('error' => 'Invalid action.'));
    }
} else {
    // Return an error message if the action POST variable is missing
    echo json_encode(array('error' => 'Action parameter is missing.'));
}
?>
