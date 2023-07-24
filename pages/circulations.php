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
    <title>Circulations | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
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
                        <h3>Circulations Management</h3>
                    </div>
                    <div class="box box-primary">
                        <div class="box-body">
                          <div class="table-responsive">
                            <table width="100%" class="table table-hover" id="circulationsTable">
                            </table>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $scripts; ?>
    <script src="../assets/js/script.js"></script>
    <script media="screen">
    $(document).ready(function() {

    <?php echo $sweetAlert; ?>
    <?php echo $ajax; ?>
    var table=$('#circulationsTable').DataTable({
    processing: true,
    ajax: {
      url: "../controllers/circulationsController.php",
      type: "POST",
      data: { action: 'select'},
      dataType: 'json',
      dataSrc: '',
      cache: false
    },
    columns: [
      { title: 'Loan ID', data: "LoanID", visible: true },
      { title: 'MemberID', data: "MemberID", visible: false },
      { title: 'Name', data: "Name", visible: true,
      render: function(data, type, row) {
        if (type === 'display' || type === 'filter') {
          return data.toLowerCase().replace(/(^|\s)\S/g, function(t) {
            return t.toUpperCase();
          });
        }
        return data;
      }
     },
      { title: 'Date Borrowed', data: "DateBorrowed", visible: true },
      { title: 'DueDate', data: "DueDate", visible: true },
      { title: 'Return Date', data: "ReturnDate", visible: true },
      { title: 'Status', data: "is_returned", visible: true,
      className: 'text-center',
        render: function (data, type, row) {
          if (data=='0') {
          var badge = '<span class="badge bg-primary">Borrowed</span> ';
        }else{
          var badge = '<span class="badge bg-success">Returned</span> ';
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
          var buttons = '<button class="btn btn-secondary btn-action-edit" tooltip="view details" data-id="' + row.LoanID + '"><i class="fa fa-eye"></i></button> ';
          return buttons;
        }
      }
    ],
    order: [[1, 'desc']]
  });
  });
    </script>
</body>

</html>
