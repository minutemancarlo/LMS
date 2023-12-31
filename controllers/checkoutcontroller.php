<?php
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SessionHandler.php';
require_once '../classess/SystemSettings.php';
// Initialize the DatabaseHandler
$db = new DatabaseHandler();
$session=new CustomSessionHandler(); 
$settings = new SystemSettings();
$memberID = $session->getSessionVariable('Id');
$config = parse_ini_file('../config.ini', true);
$sms_api = $config['sms']['key'];
$sms_borrow = $config['sms']['borrow'];
$sms_duedate = $config['sms']['duedate'];
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
                'message' => 'No books found'
            );
        }

}


if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'checkout') {
        $dateBorrowed = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+3 days'));

        // Insert into loan table
        $loanID=generateUniqueID();
        $insertLoanData = array(
            'LoanID'=>(string)$loanID,
            'MemberID' => $memberID,
            'is_returned' => 0,
            'status' => 0
        );
        $insertLoanResult = $db->insert('loan', $insertLoanData);

        if ($insertLoanResult) {


            // Select bookID from cart where memberID = $memberID
            $cartResult = $db->select('cart', 'BookID', 'MemberID=' . $memberID);

            if ($cartResult && $cartResult->num_rows > 0) {
                   // Insert into loaninfo table
                   $insertValues = '';
                   while ($row = $cartResult->fetch_assoc()) {
                       $bookID = $row['BookID'];
                       $insertValues .= "('$loanID', $bookID),";
                   }

                   $insertValues = rtrim($insertValues, ',');
                   $insertQuery = "INSERT INTO loaninfo (LoanID, BookID) VALUES $insertValues";

                   $insertResult = $db->executeQuery($insertQuery);

                   if ($insertResult) {
                     $db->delete('cart','memberID='.$memberID);
                     $memberResult = $db->select('member', '*', 'MemberID=' . $memberID);
                     $phone = "";
                     $name = "";
                     while ($row = $memberResult->fetch_assoc()) {
                         $phone = $row['Phone'];
                         $name = strtoupper($row['Name']);
                     }
                     $msgString=$sms_borrow;
                     $msgString = str_replace('{Name}', $name, $msgString);

                       $ch = curl_init();
                       $parameters = array(
                           'key' => $sms_api, // Your API KEY
                           'phone' => $phone,
                           'message' => $msgString,
                           'senderName' => 'SEMAPHORE',
                           'messageType' => 'single'
                       );



                       curl_setopt($ch, CURLOPT_URL, 'https://dev.x10.bz/emailApi/sms/send.php');
                       curl_setopt($ch, CURLOPT_POST, 1);

                       curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));

                       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                       $output = curl_exec($ch);
                       curl_close($ch);
                       $data = json_decode($output, true);

                       foreach ($data as $entry) {
                           $messageId = $entry['message_id'];
                           $recipient = $entry['recipient'];
                           $message = $entry['message'];
                           $logEntry = "Message ID: $messageId, Recipient: $recipient, Message: $message";
                           $settings->createLogFile("SMSApi", $logEntry);
                       }
                       $response = array(
                           'success' => true,
                           'message' => 'Transaction successfully queued.'
                       );
                   } else {
                       $response = array(
                           'success' => false,
                           'message' => 'Failed to insert borrow request.'
                       );
                   }
               } else {
                $response = array(
                    'success' => false,
                    'message' => 'No books found in the selection.'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Failed to insert borrow request.'
            );
        }
    }
}

function generateUniqueID() {
    $prefix = substr(uniqid(), -5); // Get the last 5 characters of the unique ID
    $randomPart = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5); // Generate a random 5-character alphanumeric string with uppercase letters and numbers

    return strtoupper($prefix . $randomPart); // Convert the result to uppercase
}



// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
