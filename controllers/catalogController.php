<?php
// Include the necessary files and initialize the database connection
require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
$settings = new SystemSettings();
$baseURL=$settings->getBaseURL();
$db = new DatabaseHandler();



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



    if ($action === "insert") {
        // Handle the image upload
        if (isset($_FILES["thumbnail"])) {
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

            if (move_uploaded_file($thumbnail["tmp_name"], $targetFile)) {



              if (isset($_POST["bookTitle"]) && isset($_POST["author"]) && isset($_POST["publication"]) && isset($_POST["isbn"]) && isset($_POST["quantity"]) && isset($_POST["genre"])) {
                $bookTitle = $_POST["bookTitle"];
                $author = $_POST["author"];
                $publication = $_POST["publication"];
                $isbn = $_POST["isbn"];
                $quantity = $_POST["quantity"];
                $genre = $_POST["genre"];

                // Escape the input values to prevent SQL injection
                $bookTitle = $db->escape($bookTitle);
                $author = $db->escape($author);
                $publication = $db->escape($publication);
                $isbn = $db->escape($isbn);
                $quantity = (int) $quantity;
                // $genre = $db->escape($genre);
                $genreArray = $db->escape(json_encode(explode(", ", $genre)));

                $fileName=$baseURL.'assets/img/thumbnail/'.$fileName;
                // Prepare the SQL query for insertion
                $query = "INSERT INTO catalog (Title, Author, Publication, ISBN, Quantity, Genre, Thumbnail) VALUES
                 ($bookTitle, $author, $publication, $isbn, $quantity, $genreArray, '$fileName')";

                // Execute the insertion query
                $insertResult = $db->executeQuery($query);

                // Check if the insertion was successful
                if ($insertResult) {
                  // Return success response
                  $response = array(
                    "success" => true,
                    "message" => "Book added successfully.",
                  );
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

            } else {
                // Image upload failed
                $response = array(
                    "success" => false,
                    "message" => "Failed to upload image.",
                );
                echo json_encode($response);
                exit;
            }
        } else {
            // No image uploaded
            $response = array(
                "success" => false,
                "message" => "Image not found in the request.",
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
