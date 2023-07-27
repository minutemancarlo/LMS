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
         dom: 'rtip',
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
          { title: '#', data: "RowNumber", visible: true },
          { title: 'bookID', data: "bookID", visible: false },
          {
            title: 'Image',
            data: "Thumbnail",
            render: function(data, type, row) {
              if (type === 'display' || type === 'filter') {
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


    });

    </script>
</body>

</html>
