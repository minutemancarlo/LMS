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
$email = $session->getSessionVariable("email");
$name= $session->getSessionVariable("Name");
$config = parse_ini_file('../config.ini', true);
$analytics=$config['analytics']['token'];
 ?>
<!doctype html>
<html lang="en">

<head>
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $analytics; ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', '<?php echo $analytics; ?>');
</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">

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
                        <h3>Borrow Books</h3>
                        <div class="container mt-5">
                          <div class="row">
                            <div class="col-md-8">
                              <div class="card mb-3">
                                <div class="card-header">
                                  My Selection
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
                                  <p hidden class="fw-bold">Return Date: <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en" name="DueDate" placeholder="Specify Return Date"></p>


                                  <hr>
                                  <button class="btn btn-primary" id="checkout">Borrow</button>
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
      var count =0;
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

        $('.datepicker-here').datepicker({
  range: true,
  multipleDatesSeparator: '-',
  language: 'en',
  onRenderCell: function (date, cellType) {
    if (cellType === 'day') {
      var currentDate = new Date();
      currentDate.setHours(0, 0, 0, 0);
      if (date.getTime() <= currentDate.getTime()) {
        return {
          disabled: true
        };
      }
    }
  }
});

      var table=$('#checkoutTable').DataTable({
        processing: true,
         dom: 'rtip',
         language: {
           emptyTable: "No items selected"
        },
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
               var buttons='<a  class="btn btn-danger btn-action-add" data-id="' + row.bookID + '"><i class="fas fa-trash"></i></a>'
              // var buttons = '<button class="btn btn-success btn-action-edit" data-id="' + row.MemberID + '"><i class="fa fa-edit"></i></button> ';
              // buttons += '<button class="btn btn-danger btn-action-delete" data-id="' + row.MemberID + '"><i class="fa fa-trash"></i></button> ';
              return buttons;
            }
          }
        ],

        initComplete: function () {
          count = table.rows().count();
          $('#cardCount').html(count);
          $('#totalBooks').html(count);
      }

      });

      $(document).on('click', '.btn-action-add', function () {

          var table = $('#checkoutTable').DataTable();
          var rowData = table.row($(this).closest('tr')).data();
          var successCallback = function(response) {
            console.log(response);
              var data = JSON.parse(JSON.stringify(response));
              if (data.success) {
      Toast.fire({
        icon: 'success',
        title: data.message
      });

      table.ajax.reload();
      count = table.rows().count()-1;

      $('#cardCount').html(count);
      $('#totalBooks').html(count);


    } else {
              Toast.fire({
              icon: 'error',
              title: data.message
            });
            }
          };

          var errorCallback = function(xhr, status, error) {
            var errorMessage = xhr.responseText;
            console.log('AJAX request error:', errorMessage);
            Toast.fire({
            icon: 'error',
            title: "Unexpected Error Occured. Please check browser logs for more info."
          });
          };
          var formData = new FormData();
          formData.append("action", "addCart");
          formData.append("bookID", rowData.bookID);
          $.ajax({
            url: '../controllers/catalogController.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false, // Important: Prevent jQuery from processing the data
            contentType: false, // Important: Prevent jQuery from setting content type
            success: successCallback,
            error: errorCallback
          });

      });

      $('#checkout').click(function() {
        var table = $('#checkoutTable').DataTable();

        if(table.rows().count()==0){
          Toast.fire({
            icon: 'error',
            title: "Please add books to your selection."
          });
          return;
        }
        let loadingAlert = Swal.fire({
        allowOutsideClick: false,
        showConfirmButton: false,
        text: 'Loading. Please wait...',
        didOpen: () => {
          Swal.showLoading();
        }
        });
        var successCallback = function(response) {
            var data = JSON.parse(JSON.stringify(response));
              if (data.success) {
                Toast.fire({
                  icon: 'success',
                  title: data.message,
                  timer: 2000,
                }).then(() => {
                  location.reload();
                });
            } else {
              Toast.fire({
                icon: 'error',
                title: data.message
              });
            }
        };

        var errorCallback = function(xhr, status, error) {
          var errorMessage = xhr.responseText;
          console.log('AJAX request error:', errorMessage);
            loadingAlert.close();
          Toast.fire({
          icon: 'error',
          title: "Unexpected Error Occured. Please check browser logs for more info."
        });
        };
        var formData = new FormData();
        formData.append("action", "checkout");
        formData.append("email", "<?php echo $email; ?>");
        formData.append("name", "<?php echo $name; ?>");

        $.ajax({
          url: '../controllers/checkoutController.php',
          type: 'POST',
          data: formData,
          dataType: 'json',
          processData: false, // Important: Prevent jQuery from processing the data
          contentType: false, // Important: Prevent jQuery from setting content type
          success: successCallback,
          error: errorCallback
        });
      });





    });
    </script>
</body>

</html>
