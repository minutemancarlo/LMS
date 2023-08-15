<?php
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';
require_once '../classess/DatabaseHandler.php';



if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $settings = new SystemSettings();
    $session = new CustomSessionHandler();
    $settings->setDefaultTimezone();
    $db = new DatabaseHandler();
    if ($action === 'select') {
      $query = "SELECT *
    FROM loan AS a
    INNER JOIN member AS b ON a.MemberID = b.MemberID
    WHERE CURDATE() > a.DueDate and STATUS=1";

      $result = $db->executeQuery($query);

      if ($result) {
          $data = array();
          while ($row = $result->fetch_assoc()) {
              $data[] = $row;
          }

          $result->close();

          header('Content-Type: application/json');
          echo json_encode($data);
    }
}

if ($action === 'sms') {
   $selectedIds = $_POST['selectedIds'];
    if (!empty($selectedIds) && is_array($selectedIds)) {
        try {

          $selectedIdsStr = implode("','", $selectedIds);
$query = "SELECT a.MemberID, Name, LoanID, Phone, Email
        FROM loan AS a
        INNER JOIN member AS b ON a.MemberID = b.MemberID
        WHERE CURDATE() > a.DueDate AND Status = 1 AND LoanID IN ('$selectedIdsStr')";



            $result = $db->executeQuery($query);

            if ($result) {
                $data = array();
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }

                $result->close();

                foreach ($data as $row) {
                    $ch = curl_init();
                    $parameters = array(
                        'key' => '4jH7kL2mP9nS6qB3rT8yV5wX2zA1cD1e', // Your API KEY
                        'phone' => $row['Phone'],
                        'message' => 'Good day ' . strtoupper($row['Name']) . "! This is to remind you for overdue book loan with Loan ID " . $row["LoanID"] . ". Please notify the library if an extension is needed. Thank you.",
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
                }

                $response = array(
                    'success' => true,
                    'message' => 'SMS Notifications Sent'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Query execution failed'
                );
            }
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            );
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}


}





 ?>
