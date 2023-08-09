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


if (isset($_POST['action']) && $_POST['action'] === "userCart") {

    $query = "Select c.LoanID,a.* from catalog a inner join loaninfo b on a.BookID=b.BookID inner join loan c on b.LoanID=c.LoanID";

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


if (isset($_POST['action']) && $_POST['action'] === "changeStatus") {

   $loanID = $_POST['loanID'];
   $newStatus = $_POST['status'];

   // Get today's date
    $today = date('Y-m-d');

    // Calculate the due date (3 days from today)
    $dueDate = date('Y-m-d', strtotime($today . ' +3 days'));

    $updateData = array(
        'status' => $newStatus,
        'DateBorrowed' => $today,
        'DueDate' => $dueDate
    );

   // Update the loan table using the DatabaseHandler class
   $updateResult = $db->update('loan', $updateData, "LoanID = '$loanID'");

   if ($updateResult) {
       $response = array(
           'success' => true,
           'message' => 'Loan status updated successfully.'
       );
   } else {
       $response = array(
           'success' => false,
           'message' => 'Failed to update loan status.'
       );
   }
   header('Content-Type: application/json');
   echo json_encode($response);


}

?>
