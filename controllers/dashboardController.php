<?php
require_once '../classess/DatabaseHandler.php';
$db = new DatabaseHandler();


if (isset($_POST['action']) && $_POST['action'] === 'overdue') {
    $query = "
        SELECT
            months.Month AS Month,
            COALESCE(SUM(CASE WHEN CURDATE() >= DueDate THEN 1 ELSE 0 END), 0) AS OverdueCount
        FROM (
            SELECT 1 AS Month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION
            SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
        ) AS months
        LEFT JOIN loan ON months.Month = MONTH(DueDate) AND Status = 1 and is_returned=0
        GROUP BY months.Month
        ORDER BY months.Month;
    ";

    $result = $db->executeQuery($query);

    $data = array();
    while ($row = $result->fetch_assoc()) {
      $data[] = $row['OverdueCount'];
    }

    // Close the result set
    $result->close();

    // Return data as JSON response
    header('Content-Type: application/json');
    echo json_encode($data);
}

if (isset($_POST['action']) && $_POST['action'] === 'circulations') {

  // Include your DatabaseHandler class and create a connection

  // Query to count Borrowed books by month
  $queryBorrowed = "
  SELECT
      IFNULL(COUNT(loan.LoanID), 0) as count
  FROM (
      SELECT 1 AS Month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION
      SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
  ) AS months
  LEFT JOIN loan ON months.Month = MONTH(DateBorrowed) AND Status = 1
  GROUP BY months.Month
  ORDER BY months.Month ASC;
  ";

  // Query to count Returned books by month
  $queryReturned = "
  SELECT
      IFNULL(COUNT(loan.LoanID), 0) as count
  FROM (
      SELECT 1 AS Month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION
      SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
  ) AS months
  LEFT JOIN loan ON months.Month = MONTH(ReturnDate) AND Status = 2
  GROUP BY months.Month
  ORDER BY months.Month ASC;
  ";

  // Execute the queries using your DatabaseHandler class
  $db = new DatabaseHandler(); // Instantiate your DatabaseHandler class

  // Execute the queries and fetch one row at a time
  $borrowedCounts = array();
  $resultBorrowed = $db->executeQuery($queryBorrowed);
  while ($row = $resultBorrowed->fetch_assoc()) {
      $borrowedCounts[] = $row['count'];
  }

  $returnedCounts = array();
  $resultReturned = $db->executeQuery($queryReturned);
  while ($row = $resultReturned->fetch_assoc()) {
      $returnedCounts[] = $row['count'];
  }

  // Combine the Borrowed and Returned counts into a single associative array
  $data = array(
      "borrowed" => $borrowedCounts,
      "returned" => $returnedCounts
  );

  // Convert the data array to JSON format
  $response = json_encode($data);

  // Send the JSON response
  header('Content-Type: application/json');
  echo $response;

}




?>
