<?php
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

$roleValue = 0; // 0 for Admin, 1 for Standard User
$roleName = $roleHandler->getRoleName($roleValue);
$menuTags = $roleHandler->getMenuTags($roleValue);
$cards = $roleHandler->getCards($roleValue);
 ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Members | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
    <link href="../assets/css/master.css" rel="stylesheet">
    <style media="screen">
    th { font-size: .8em; }
    td { font-size: .8em; }
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
                      <h3>Member Management</h3>
                           <div class="card">
                             <div class="card-body ">
                               <div class="table-responsive">
                                 <table width="100%" class="table table-hover" id="memberTable">
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
       $('button[name="filterBtn"]').click(function() {
        event.preventDefault();

            var successCallback = function(response) {
              console.log(response);
                var data = JSON.parse(JSON.stringify(response));
              if (data.success) {
                Toast.fire({
                  icon: 'success',
                  title: 'Fetching logs..',
                  timer: 2000,
                }).then(() => {
                   $("#logsContent").val(data.message);
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
              Toast.fire({
              icon: 'error',
              title: "Unexpected Error Occured. Please check browser logs for more info."
            });
            };
            var data = {
              filterdate: $("#filterdate").val(),
            };
            loadContent('../controllers/logsController.php', data, successCallback, errorCallback);
        });

        var table=$('#memberTable').DataTable({
        processing: true,
        ajax: {
          url: "../controllers/memberController.php",
          type: "POST",
          data: { action: 'select'},
          dataType: 'json',
          dataSrc: '',
          cache: false
        },
        columns: [
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
          { title: 'Email', data: "Email", visible: true },
          { title: 'Phone', data: "Phone", visible: true },
          {
            title: 'Status',
            data: "is_verified",
            className: "text-center",
            visible: true,
            render: function(data, type, row) {
        var svgFile = data == 1 ? "../assets/img/check-circle-solid.svg" : "../assets/img/times-circle-solid.svg";
        var svgElement = '<img src="' + svgFile + '" alt="SVG Icon" width="20" height="20">';
        return svgElement;
      }

          },
          { title: 'Created On', data: "Created_On", visible: true },
          { title: 'Updated On', data: "Updated_On", visible: true },
          {
            title: 'Action',
            data: null,
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
              var buttons = '<button class="btn btn-success btn-action-edit" data-id="' + row.MemberID + '"><i class="fa fa-edit"></i></button> ';
              buttons += '<button class="btn btn-danger btn-action-delete" data-id="' + row.MemberID + '"><i class="fa fa-trash"></i></button> ';
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
