<?php
// Include the necessary files and initialize the database connection
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';
$settings = new SystemSettings();
$baseURL=$settings->getBaseURL();
$db = new DatabaseHandler();
$session=new CustomSessionHandler();
$memberID=$session->getSessionVariable('Id');

function getBookIDsFromCart() {

        $result = $db->select("cart", "bookID", "memberID=$memberID");
        $bookIDs = array_column($result->fetch_all(MYSQLI_ASSOC), 'bookID');

        return $bookIDs;

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = isset($_POST["action"]) ? $_POST["action"] : "";

    if (isset($_POST['action']) && isset($_POST['item'])) {
        $action = $_POST['action'];
        $item = $_POST['item'];

        // Create a new instance of the DatabaseHandler


        // Check the action parameter and perform the appropriate operation
        if ($action === 'select') {
            // Fetch data based on the item value (in this case, 'books')
            if ($item === 'books') {
                // Perform the select query to retrieve book data
                $query = "SELECT catalog.*, (catalog.Quantity - COALESCE((SELECT COUNT(*) FROM loaninfo WHERE loaninfo.BookID = catalog.BookID), 0)) as Remaining FROM catalog";

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

    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        if (isset($_POST['bookID'])) {
            $bookID = $_POST['bookID'];
            $result = $db->delete('catalog', "BookID = $bookID");
            if ($result) {
                $response = array('success' => true, 'message' => 'Book deleted successfully.');
            } else {
                $response = array('success' => false, 'message' => 'Error deleting book.');
            }

            echo json_encode($response);
            exit;
        } else {
            $response = array('success' => false, 'message' => 'BookID is missing.');
            echo json_encode($response);
            exit;
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'checkCart') {
      $result = $db->select("cart", "bookID", "memberID=$memberID");
      $bookIDs = array_column($result->fetch_all(MYSQLI_ASSOC), 'bookID');


        echo json_encode(['bookIDs' => $bookIDs]);
        exit;
    }
    if (isset($_POST['action'])&& $_POST['action'] === 'addCart') {

      if (isset($_POST['action']) && $_POST['action'] === 'addCart') {

      $bookID = $_POST['bookID'];

      $db = new DatabaseHandler();
      try {


          $result = $db->select("cart", "bookID", "memberID = $memberID AND bookID = $bookID");
          $bookExists = $result->num_rows > 0;

          if ($bookExists) {
              // Book exists in cart, delete it

              $db->delete("cart", "memberID=$memberID and bookID=$bookID");

              $response = array(
                  'success' => true,
                  'message' => 'Book removed from selection'
              );
          } else {
              // Book does not exist in cart, insert it
              unset($_POST['action']);
              $_POST['memberID'] = $memberID;

              $insertResult = $db->insert('cart', $_POST);

              if ($insertResult) {
                  $response = array(
                      'success' => true,
                      'message' => 'Book added to selection'
                  );
              } else {
                  // Failed to insert
                  $response = array(
                      'success' => false,
                      'message' => 'Failed to add book to selection'
                  );
              }
          }

          echo json_encode($response);
          exit;
      } catch (Exception $e) {
          echo json_encode(['error' => 'Failed to process selection data']);
          exit;
      }
  }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

    if ($action === "insert") {
        // Handle the image upload
        if (isset($_FILES["thumbnail"])&&$_FILES["thumbnail"]["size"] > 0) {

            $thumbnail = $_FILES["thumbnail"];
            if (strpos($baseURL, "8080") !== false) {
              $rootFolder = $_SERVER['DOCUMENT_ROOT'] . '/LMS/';
            } else {
              $rootFolder = $_SERVER['DOCUMENT_ROOT'] . '/';
            }
            $uploadDir =   $rootFolder."assets/img/thumbnail/";
            $uniqueId = uniqid();
            $fileName = $uniqueId . "_" . date("YmdHis") . "_" . $thumbnail["name"];
            $targetFile = $uploadDir . $fileName;
            $fileName= isset($_FILES["thumbnail"])?'thumbnail/'.$fileName:'book.png';
            $fileName=$baseURL.'assets/img/'.$fileName;
            $_POST['thumbnail']=$fileName;
            if (move_uploaded_file($thumbnail["tmp_name"], $targetFile)) {

            } else {
                // Image upload failed
                $response = array(
                    "success" => false,
                    "message" => "Failed to upload image.",
                );
                echo json_encode($response);
                exit;
            }
        }



          $bookID=$_POST['bookID'];
          if (isset($_POST["bookTitle"]) && isset($_POST["author"]) && isset($_POST["publication"]) && isset($_POST["isbn"]) && isset($_POST["quantity"]) && isset($_POST["genre"])) {
            $bookTitle = $_POST["bookTitle"];
            $author = $_POST["author"];
            $publication = $_POST["publication"];
            $isbn = $_POST["isbn"];
            $quantity = $_POST["quantity"];
            $genre = $_POST["genre"];
            $desc = isset($_POST["description"])?$_POST["description"]:'';

            // Escape the input values to prevent SQL injection
            $bookTitle = $db->escape($bookTitle);
            $author = $db->escape($author);
            $publication = $db->escape($publication);
            $isbn = $db->escape($isbn);
            $desc= $db->escape($desc);
            $quantity = (int) $quantity;
            $genreArray = $db->escape(json_encode(explode(", ", $genre)));
            $fileName=isset($fileName)?$fileName:null;

            if ($bookID=='0'||$bookID=='') {

              $query = "INSERT INTO catalog (Title, Description, Author, Publication, ISBN, Quantity, Genre, Thumbnail) VALUES
               ($bookTitle, $desc , $author, $publication, $isbn, $quantity, $genreArray, '$fileName')";

               $insertResult = $db->executeQuery($query);

               $response = array(
                 "success" => true,
                 "message" => "Book added successfully.",
               );
            }else{
              unset($_POST['bookID']);
              unset($_POST['action']);
              $_POST['Title']=$_POST['bookTitle'];
              unset($_POST['bookTitle']);
              $genreValue = $_POST['genre'];
              $genreArray = explode(',', $genreValue);
              $genreJson = json_encode($genreArray);
              $_POST['genre']=$genreJson;
               $insertResult = $db->update('catalog',$_POST,'bookID='.$bookID);
               $response = array(
                 "success" => true,
                 "message" => "Book updated successfully.",
               );
            }




            // Check if the insertion was successful
            if ($insertResult) {
              echo json_encode($response);
              exit;
            } else {
              // Return error response
              $response = array(
                "success" => false,
                "message" => "Failed to insert book into the database.",
              );
              echo json_encode($response);
              exit;
            }
          } else {
            // Required fields not found in the request
            $response = array(
              "success" => false,
              "message" => "Required fields not found in the request.",
            );
            echo json_encode($response);
            exit;
          }




    }
} else {
    // Invalid request method
    http_response_code(400);
    $response = array(
        "success" => false,
        "message" => "Invalid request method.",
    );
    echo json_encode($response);
    exit;
}





?>
