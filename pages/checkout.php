<?php
require_once '../classess/SessionHandler.php';
$session = new CustomSessionHandler();
if($session->isSessionVariableSet("Role")){
  if ($session->getSessionVariable('Role')=='0') {
    header("Location: ../pages/");
  }
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
$validate = $settings->validateForms();
$roleValue = $session->getSessionVariable("Role");
$roleName = $roleHandler->getRoleName($roleValue);
$menuTags = $roleHandler->getMenuTags($roleValue);
 ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Checkout | <?php echo $websiteTitle; ?></title>
      <?php echo $styles; ?>
      <style media="screen">
      table.dataTable td, table.dataTable th {
    font-size: 15px; /* Adjust the font size to your desired value */
  }
      </style>
    <link href="../assets/css/master.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
      <?php include 'sidebar.php'; ?>
        <div id="body" class="active">
          <?php include 'navbar.php'; ?>
            <div class="content">
                <div class="container-fluid">
                    <div class="page-title">
                        <h3>Checkout</h3>
                        <div class="container mt-5">
                          <div class="row">
                            <div class="col-md-8">
                              <div class="card mb-3">
                                <div class="card-header">
                                  Checkout Cart
                                </div>
                                <div class="card-body">
                                  <div class="row g-3">
                                    <div class="box box-primary">
                                      <div class="box-body">
                                        <div class="table-responsive">
                                          <table width="100%" class="table table-hover" id="checkoutTable">
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="card">
                                <div class="card-header">
                                  Reservation Summary
                                </div>
                                <div class="card-body">
                                  <p class="fw-bold">Total Books: <span id="totalBooks">0</span></p>
                                  <button class="btn btn-primary">Checkout</button>
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
    <script type="text/javascript">
    $(document).ready(function() {
      <?php echo $sweetAlert; ?>
      <?php echo $ajax; ?>

      // Continuously send AJAX request every 10 seconds
        var timer = setInterval(function() {
            $.ajax({
                url: '../controllers/sessionController.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    var data = JSON.parse(JSON.stringify(response));
                    if (data.success) {
                        // Show the session expired prompt using SweetAlert2
                        clearInterval(timer);
                        Swal.fire({
                            title: 'Session Expired!',
                            text: data.message,
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonText: 'Confirm'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Reload the page
                                location.reload();
                            }
                        });
                    }
                }
            });
        }, 10000); // 10 seconds interval

      var table=$('#checkoutTable').DataTable({
        processing: true,
         dom: 'rtip',
        ajax: {
          url: "../controllers/checkoutController.php",
          type: "POST",
          data: { action: 'select'},
          dataType: 'json',
          dataSrc: '',
          cache: false
        },
        columns: [
          { title: '#', data: "RowNumber", visible: true },
          { title: 'bookID', data: "bookID", visible: false },
          {
            title: 'Image',
            data: "Thumbnail",
            render: function(data, type, row) {
              if (type === 'display' || type === 'filter') {
            // Add the default image URL here
              var defaultImage = '../assets/img/book.png';
              return '<img src="' + (data!='' ? data : defaultImage) + '" class="img-thumbnail" alt="Thumbnail" style="width: 100px;height:100px">';
            }
            return data;
          }
        },
          { title: 'Title', data: "Title", visible: true,
          render: function(data, type, row) {
            if (type === 'display' || type === 'filter') {
              return data.toLowerCase().replace(/(^|\s)\S/g, function(t) {
                return t.toUpperCase();
              });
            }
            return data;
          }
         },

          {
            title: 'Remove',
            data: null,
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
               var buttons='<a  class="btn btn-danger btn-action-edit" data-id="' + row.bookID + '"><i class="fas fa-trash"></i></a>'
              // var buttons = '<button class="btn btn-success btn-action-edit" data-id="' + row.MemberID + '"><i class="fa fa-edit"></i></button> ';
              // buttons += '<button class="btn btn-danger btn-action-delete" data-id="' + row.MemberID + '"><i class="fa fa-trash"></i></button> ';
              return buttons;
            }
          }
        ],

        initComplete: function () {
          var rowCount = table.rows().count();
    $('#cardCount').html(rowCount);
    $('#totalBooks').html(rowCount);

      }

      });
    });
    </script>
</body>

</html>
