<?php
// Include the necessary files and initialize the database connection
require_once '../classess/DatabaseHandler.php';

// Check if the action and item parameters are set in the POST request
if (isset($_POST['action']) && isset($_POST['item'])) {
    $action = $_POST['action'];
    $item = $_POST['item'];

    // Create a new instance of the DatabaseHandler
    $db = new DatabaseHandler();

    // Check the action parameter and perform the appropriate operation
    if ($action === 'select') {
        // Fetch data based on the item value (in this case, 'books')
        if ($item === 'books') {
            // Perform the select query to retrieve book data
            $query = "SELECT catalog.*, (catalog.Quantity - COALESCE((SELECT COUNT(*) FROM loan WHERE loan.BookID = catalog.BookID), 0)) as Remaining FROM catalog";

            $result = $db->executeQuery($query);


            // Convert the result into an associative array
            $booksData = [];
            while ($row = $result->fetch_assoc()) {
                $booksData[] = $row;
            }



            // Return the data as JSON
            header('Content-Type: application/json');
            echo json_encode($booksData);
        } else {
            // Handle other items here if necessary
            // ...
        }
    } else {
        // Handle other actions here if necessary
        // ...
    }
}
?>
