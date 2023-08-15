<?php
require_once '../classess/SessionHandler.php';
$session = new CustomSessionHandler();
if(!$session->isSessionVariableSet("Role")){
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
$roleName = $roleHandler->getRoleName($roleValue);
$menuTags = $roleHandler->getMenuTags($roleValue);
$result=$db->select('loan','*','is_returned=0');
$borrowed=$result->num_rows;
$overdue=0;
$result=$db->select('member','*','');
$users=$result->num_rows;
$result=$db->select('member','*','is_verified=0');
$unverified=$result->num_rows;
$cards = $roleHandler->getCards($roleValue,$borrowed,$overdue,$users,$unverified);
 ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notifications | <?php echo $websiteTitle; ?> </title>
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
                        <h3>Notifications</h3>
                        <div class="box box-primary">
                            <div class="box-body">
                              <div class="table-responsive">
                                <table width="100%" class="table table-hover" id="notifTable">
                                </table>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <?php echo $scripts; ?>
   <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>
    <script src="../assets/js/script.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      <?php echo $sweetAlert; ?>
      <?php echo $ajax; ?>
      var count=0;
      // Continuously send AJAX request every 10 seconds
        var timer = setInterval(function() {
            $.ajax({
                url: '../controllers/sessionController.php',
                type: 'GET',
                dataType: 'json',
                dom: 'Bfrtip',
                buttons: ['pdf'],
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


        var notifTable = $('#notifTable').DataTable({
        processing: true,
        ajax: {
            url: "../controllers/notificationsController.php",
            type: "POST",
            data: { action: 'select' },
            dataType: 'json',
            dataSrc: '',
            cache: false
        },
        dom: 'Bfrtip',
        buttons: [
    {
        text: '<i class="fas fa-comment"></i> Send via SMS',
        className: 'btn btn-primary',
        action: function (e, dt, node, config) {
            var selectedLoanIDs = [];

            // Get the selected LoanIDs from the checkboxes
            $('.select-checkbox:checked', notifTable.rows().nodes()).each(function() {
                var row = notifTable.row($(this).closest('tr')).data();
                selectedLoanIDs.push(row.LoanID);
            });

            if (selectedLoanIDs.length === 0) {
          Toast.fire({
              icon: 'info',
              title: 'No items selected for SMS'
          });
          return;
      }


      // Show loading alert
let loadingAlert = Swal.fire({
allowOutsideClick: false,
showConfirmButton: false,
text: 'Loading. Please wait...',
didOpen: () => {
  Swal.showLoading();
}
});

// Send AJAX request
$.ajax({
url: '../controllers/notificationsController.php',
type: 'POST',
data: {
  action: 'sms',
  selectedIds: selectedLoanIDs
},
success: function(response) {
  // console.log(response);
  // Close loading alert
  loadingAlert.close();

  var data = JSON.parse(JSON.stringify(response));
  if (data.success) {
      Toast.fire({
          icon: 'success',
          title: data.message,
          timer: 2000,
      }).then(() => {
          //location.reload();
          // window.location.href = window.origin+'/lms/admin';
      });
  } else {
      Toast.fire({
          icon: 'error',
          title: data.message
      });
  }
},
error: function(xhr, status, error) {

  loadingAlert.close();

  var errorMessage = xhr.responseText;
  console.log('AJAX request error:', errorMessage);
  Toast.fire({
      icon: 'error',
      title: "Unexpected Error Occurred. Please check browser logs for more info."
  });
}
});

        }
    }
],

        columns: [
            {
                title: '<input type="checkbox" id="selectAllCheckbox">',
                data: null,
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function(data, type, row) {
                    return '<input type="checkbox" class="select-checkbox">';
                }
            },
            { title: 'Member ID', data: "MemberID", visible: false },
            { title: 'Loan ID', data: "LoanID", visible: true },
            { title: 'Date Borrowed', data: "DateBorrowed", visible: true },
            { title: 'Due Date', data: "DueDate", visible: true }
        ],
    });


    // Click event for the "Select All" checkbox in the header
        $(document).on('click', '#selectAllCheckbox', function() {
            $('.select-checkbox', notifTable.rows().nodes()).prop('checked', this.checked);
        });

        // Click event for individual row checkboxes
        $(document).on('click', '.select-checkbox', function() {
            if (!this.checked) {
                $('#selectAllCheckbox').prop('checked', false);
            }
        });




    });

    </script>
</body>

</html>
