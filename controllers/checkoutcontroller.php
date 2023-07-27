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


                // Return the data as JSON
                echo json_encode($cartItems);
                exit();
        } else {
            // No cart items found
            $response = array(
                'success' => false,
                'message' => 'No cart items found'
            );
        }

} else {
    // Invalid action parameter
    $response = array(
        'success' => false,
        'message' => 'Invalid action'
    );
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
