<?php
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SessionHandler.php';
// Initialize the DatabaseHandler
$db = new DatabaseHandler();
$session=new CustomSessionHandler();
$memberID = $session->getSessionVariable('Id');
// Check if the action is set and equals 'select'
if (isset($_POST['action']) && $_POST['action'] === 'select') {

        // Sanitize the memberID value to prevent SQL injection


        // SQL query to select cart items joined with the catalog table
        $query = "SELECT
    (@row_number := @row_number + 1) AS RowNumber,
    cart.*,
    catalog.BookID,
    catalog.Title,
    catalog.Thumbnail
FROM
    cart
INNER JOIN
    catalog ON cart.BookID = catalog.BookID
CROSS JOIN
    (SELECT @row_number := 0) AS rn
                  WHERE cart.memberID = $memberID";

        // Execute the query and get the result
        $result = $db->executeQuery($query);

        if ($result->num_rows > 0) {
            // Fetch the rows and store them in an array
            $cartItems = array();
            while ($row = $result->fetch_assoc()) {
                $cartItems[] = $row;
            }
                echo json_encode($cartItems);
                exit();
        } else {
            // No cart items found
            $response = array(
                'success' => false,
                'message' => 'No cart items found'
            );
        }

}


if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'checkout') {
        $dateBorrowed = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+14 days'));

        // Insert into loan table
        $insertLoanData = array(
            'memberID' => $memberID,
            'dateBorrowed' => $dateBorrowed,
            'dueDate' => $dueDate,
            'is_returned' => 0,
            'status' => 0
        );

        $insertLoanResult = $db->insert('loan', $insertLoanData);

        if ($insertLoanResult) {
          $loanID = $db->getLastInsertID();

            // Select bookID from cart where memberID = $memberID
            $cartResult = $db->select('cart', 'bookID', 'memberID=' . $memberID);

            if ($cartResult && $cartResult->num_rows > 0) {
                   // Insert into loaninfo table
                   $insertValues = '';
                   while ($row = $cartResult->fetch_assoc()) {
                       $bookID = $row['bookID'];
                       $insertValues .= "($loanID, $bookID),";
                   }

                   $insertValues = rtrim($insertValues, ',');
                   $insertQuery = "INSERT INTO loaninfo (loanID, bookID) VALUES $insertValues";

                   $insertResult = $db->executeQuery($insertQuery);

                   if ($insertResult) {
                     $db->delete('cart','memberID='.$memberID);
                       $response = array(
                           'success' => true,
                           'message' => 'Transaction successfully queued.'
                       );
                   } else {
                       $response = array(
                           'success' => false,
                           'message' => 'Failed to insert loan information.'
                       );
                   }
               } else {
                $response = array(
                    'success' => false,
                    'message' => 'No books found in the cart.'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Failed to insert loan data.'
            );
        }
    }
}





// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
