<?php
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SessionHandler.php';
// Initialize the DatabaseHandler
$db = new DatabaseHandler();
$session=new CustomSessionHandler();
$memberID = $session->getSessionVariable('Id');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'select') {
    $selectResult = $db->select('loan','*','memberID='.$memberID);

    if ($selectResult && $selectResult->num_rows > 0) {
      $loanData = array();
      $rowNumber = 1; // Initialize row number
      while ($row = $selectResult->fetch_assoc()) {
          // Add row number to the row data
          $row['rowNumber'] = $rowNumber;
          $loanData[] = $row;
          $rowNumber++; // Increment row number for the next row
      }

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode($loanData);
        exit;
    } else {
        $response = array(
            'success' => false,
            'message' => 'No data available in history.'
        );
    }
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request.'
    );
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
