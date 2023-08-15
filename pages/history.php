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

    <!-- MODALS -->
    <div class="modal fade" id="viewLoan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
           <div class="modal-dialog modal-lg">
               <div class="modal-content">
                   <!-- Modal Header -->
                   <div class="modal-header">
                       <h5 class="modal-title" id="exampleModalLabel">Loan ID: <strong id="loanID"></strong> </h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                   </div>
                   <!-- Modal Body -->
                   <div class="modal-body">
                     <div class="box box-primary">
                         <div class="box-body">
                           <div class="table-responsive">
                             <table width="100%" class="table table-hover" id="userCartTable">
                             </table>
                           </div>
                         </div>
                     </div>
                   </div>
                   <!-- Modal Footer -->
                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

        var cartTable=$('#userCartTable').DataTable({
        processing: true,
        dom: 'rtip',
        ajax: {
          url: "../controllers/circulationsController.php",
          type: "POST",
          data: { action: 'userCart'},
          dataType: 'json',
          dataSrc: '',
          cache: false
        },
        columns: [
          { title: 'Loan ID', data: "LoanID", visible: false },

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
          { title: 'Author', data: "Author", visible: true,
          render: function(data, type, row) {
            if (type === 'display' || type === 'filter') {
              return data.toLowerCase().replace(/(^|\s)\S/g, function(t) {
                return t.toUpperCase();
              });
            }
            return data;
          }
        },
          { title: 'ISBN', data: "ISBN", visible: true},
          { title: 'Publication', data: "Publication", visible: true,
          render: function(data, type, row) {
            if (type === 'display' || type === 'filter') {
              return data.toLowerCase().replace(/(^|\s)\S/g, function(t) {
                return t.toUpperCase();
              });
            }
            return data;
          }
         },
        ],
      });

      $(document).on('click', '.btn-action-view', function() {
         $('#viewLoan').modal('show');
         var id = $(this).data('id');
         $('#loanID').html(id);
         cartTable.search(id).draw();
       });

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
               var buttons='<a  class="btn btn-primary btn-action-view" data-id="' + data.LoanID + '"><i class="fas fa-eye"></i></a>'
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
