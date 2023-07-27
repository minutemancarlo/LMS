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
    <title>History | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
    <link href="../assets/css/master.css" rel="stylesheet">
    <style media="screen">
    table.dataTable td, table.dataTable th {
  font-size: 15px; /* Adjust the font size to your desired value */
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
                        <h3>Transaction History</h3>
                        <div class="box box-primary">
                          <div class="box-body">
                            <div class="table-responsive">
                              <table width="100%" class="table table-hover" id="historyTable">
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
    <script src="../assets/js/script.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      <?php echo $sweetAlert; ?>
      <?php echo $ajax; ?>
      var table=$('#historyTable').DataTable({
        processing: true,
         language: {
           emptyTable: "No transaction history"
        },
        ajax: {
          url: "../controllers/historyController.php",
          type: "POST",
          data: { action: 'select'},
          dataType: 'json',
          dataSrc: '',
          cache: false
        },
        columns: [
          { title: '#', data: "rowNumber", visible: true },
          { title: 'Loan ID', data: "LoanID", visible: true },
          { title: 'Date Borrowed', data: "DateBorrowed", visible: true },
          { title: 'Due Date', data: "DueDate", visible: true},
          { title: 'Date Returned', data: "ReturnDate", visible: true },
          { title: 'Status', data: "status", visible: true,
            className: 'text-center',
            render: function (data, type, row) {
              var badge;
              var today = new Date();

              if (data === '0') {
                badge = '<span class="badge bg-warning" style="color: black;">Pending</span> ';
              } else if (data === '2') {
                badge = '<span class="badge bg-success">Returned</span> ';
              } else {

                if (new Date(data.DueDate) >= today) {
                  badge = '<span class="badge bg-danger">Overdue</span>';
                } else {
                  badge = '<span class="badge bg-primary">Borrowed</span>';
                }
              }
              return badge;
            }

         },
          {
            title: 'Action',
            data: null,
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
               var buttons='<a  class="btn btn-primary btn-action-add" data-id="' + data.LoanID + '"><i class="fas fa-eye"></i></a>'
              // var buttons = '<button class="btn btn-success btn-action-edit" data-id="' + row.MemberID + '"><i class="fa fa-edit"></i></button> ';
              // buttons += '<button class="btn btn-danger btn-action-delete" data-id="' + row.MemberID + '"><i class="fa fa-trash"></i></button> ';
              return buttons;
            }
          }
        ]
    });
  });

    </script>
</body>

</html>
