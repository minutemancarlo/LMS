<?php
require_once '../classess/SessionHandler.php';
$session = new CustomSessionHandler();
if (!$session->isSessionVariableSet("Role")) {
    header("Location: ../");
}

require_once '../classess/DatabaseHandler.php';
require_once '../classess/SystemSettings.php';
require_once '../classess/RoleHandler.php';
$settings = new SystemSettings();
$db = new DatabaseHandler();
$roleHandler = new RoleHandler();
$websiteTitle = $settings->getWebsiteTitle();
$styles = $settings->getStyles();
$scripts = $settings->getScripts();
$sweetAlert = $settings->getSweetAlertInit();
$ajax = $settings->getAjaxInit();
$settings->setDefaultTimezone();
$baseURL = $settings->getBaseURL();
$session->checkSessionExpiration();
$roleValue = $session->getSessionVariable("Role");
$roleId = $session->getSessionVariable("profId");
$Name = $session->getSessionVariable("Name");
$email = $session->getSessionVariable("email");
$contact = $session->getSessionVariable("phone");
$roleName = $roleHandler->getRoleName($roleValue);
$menuTags = $roleHandler->getMenuTags($roleValue);
$result = $db->select('loan', '*', 'is_returned=0');
$borrowed = $result->num_rows;
$overdue = 0;
$result = $db->select('member', '*', '');
$users = $result->num_rows;
$result = $db->select('member', '*', 'is_verified=0');
$unverified = $result->num_rows;
$cards = $roleHandler->getCards($roleValue, $borrowed, $overdue, $users, $unverified);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Profile | <?php echo $websiteTitle; ?> </title>
    <?php echo $styles; ?>
    <link href="../assets/css/master.css" rel="stylesheet">
    <style>
        /* Additional styles for the card */
        .card {
            max-width: 400px;
            margin: 0 auto;
            background-color: #f2f2f2;
            position: relative;
        }

        /* Curved shape at the top */
        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background-color: #007bff;
            /* Adjust the background color as desired */
            border-radius: 50% 50% 0 0;
        }

        /* Center the card body content */
        .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            position: relative;
            z-index: 1;
            /* Ensure the card body is above the curved shape */
        }

        /* Optional styles for the text */
        h3 {
            margin-bottom: 10px;
        }

        h4 {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div id="body" class="active">
            <?php include 'navbar.php'; ?>
            <div class="content">
                <div class="container-fluid">
                    <div class="page-title">
                        <h3>My Profile</h3>
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body" style="font-size: small;">
                                                <h3 style="color: white;">LMS E-Card</h3>
                                                <!-- Profile Image -->
                                                <img src="../assets/img/user.png" alt="Profile Image" width="150" height="150" style="border-radius: 50%;">
                                                <div style="font-size: 16px;">
                                                    <!-- Name -->
                                                    <p><?php echo ucwords($Name); ?></p>

                                                    <!-- Email -->
                                                    <p><?php echo $email; ?></p>

                                                    <!-- Contact Number -->
                                                    <p><?php echo $contact; ?></p>
                                                </div>
                                                <!-- Sample Barcode -->
                                                <canvas style="width: 100%" id="barcodeContainer">barcode</canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="font-size: 16px;" class="col-md-9">
                                    <div class="box box-success">
                                        <div class="box-body">
                                        <form>
                                            <div class="form-group">
                                                <label for="profilePicture">Profile Picture:</label>
                                                <input type="file" class="form-control-file" id="profilePicture" name="profilePicture">
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="fullName">Full Name:</label>
                                                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="phoneNumber">Phone Number:</label>
                                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email:</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password:</label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirmPassword">Confirm Password:</label>
                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                        </div>
                                    </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $scripts; ?>
    <script src="../assets/js/script.js"></script>
    <script src="https://unpkg.com/bwip-js"></script>

    <script>
        $(document).ready(function() {
            <?php //echo $sweetAlert; 
            ?>
            <?php //echo $ajax; 
            ?>
            // var count = 0;
            // // Continuously send AJAX request every 10 seconds
            // var timer = setInterval(function() {
            //     $.ajax({
            //         url: '../controllers/sessionController.php',
            //         type: 'GET',
            //         dataType: 'json',
            //         dom: 'Bfrtip',
            //         buttons: ['pdf'],
            //         success: function(response) {
            //             console.log(response);
            //             var data = JSON.parse(JSON.stringify(response));
            //             if (data.success) {
            //                 // Show the session expired prompt using SweetAlert2
            //                 clearInterval(timer);
            //                 Swal.fire({
            //                     title: 'Session Expired!',
            //                     text: data.message,
            //                     icon: 'warning',
            //                     showCancelButton: false,
            //                     confirmButtonText: 'Confirm'
            //                 }).then((result) => {
            //                     if (result.isConfirmed) {
            //                         // Reload the page
            //                         location.reload();
            //                     }
            //                 });
            //             }
            //         }
            //     });
            // }, 10000); // 10 seconds interval
            // Default barcode data
            var defaultBarcodeData = "<?php echo $roleId;  ?>";

            // Generate the barcode on document load
            generateBarcode(defaultBarcodeData);

        });

        function generateBarcode(barcodeData) {
            // Options for barcode generation (change as needed)
            var options = {
                bcid: 'code128', // Barcode type (e.g., 'code128', 'ean13', 'qrcode', etc.)
                text: barcodeData, // Barcode data to encode
                scale: 3, // Scaling factor
                height: 8, // Height of the barcode
                includetext: true, // Show the data below the barcode
                textxalign: 'center', // Horizontal alignment of the data
            };

            // Generate the barcode
            bwipjs.toCanvas('barcodeContainer', options, function(err, canvas) {
                if (err) {
                    // Handle error
                    console.log("Error: " + err);
                } else {
                    // Barcode successfully generated
                    console.log("Barcode generated!");
                }
            });
        }
    </script>

</body>

</html>