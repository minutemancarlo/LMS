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
                        <div class="row">
                          <div class="col-md-4">
                            <div class="box box-primary">
                              <div class="box-body">
                                <sub>Legend:
                                  <span class="badge bg-warning" style="color: black;">Pending</span>
                                <span class="badge bg-success">Returned</span>
                                <span class="badge bg-primary">Borrowed</span>
                                <span class="badge bg-danger">Overdue</span>
                              </sub>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-8">
                            <div class="box box-primary">
                              <div class="box-body">
                                <sub>
                                  <form class="" action="index.html" method="post">
                                    <div class="row">


                                      <div class="col">
                                        <strong>
                                          Status
                                        </strong>
                                        <select class="form-control" name="">
                                          <option value="" selected>All</option>
                                          <option value="">Borrowed</option>
                                          <option value="">Pending</option>
                                          <option value="">Overdue</option>
                                          <option value="">Returned</option>
                                        </select>
                                      </div>
                                      <div class="col">
                                        <strong>
                                          Borrowed Date
                                        </strong>
                                        <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en">
                                      </div>
                                      <div class="col">
                                        <strong>
                                          Return Date
                                        </strong>
                                        <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en">
                                      </div>
                                      <div class="col">
                                        <strong>
                                          Date Returned
                                        </strong>
                                        <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en">
                                      </div>
                                      <div class="col">
                                        <button type="button" class="btn btn-primary" name="button">Search</button>
                                      </div>
                                    </div>

                                  </form>

                                </sub>
                              </div>
                            </div>
                          </div>

                        </div>
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

    <!-- MODALS -->
    <div class="modal fade" id="viewLoan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
           <div class="modal-dialog">
               <div class="modal-content">
                   <!-- Modal Header -->
                   <div class="modal-header">
                       <h5 class="modal-title" id="exampleModalLabel">Loan ID: <strong id="loanID"></strong> </h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                   </div>
                   <!-- Modal Body -->
                   <div class="modal-body">
                       <!-- Add your content here -->
                       <p>This is a blank modal. Add your content here.</p>
                   </div>
                   <!-- Modal Footer -->
                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                       <button type="button" class="btn btn-primary">Save changes</button>
                   </div>
               </div>
           </div>
       </div>


    <!-- MODALS -->
    <?php echo $scripts; ?>
    <script src="../assets/js/script.js"></script>
    <script media="screen">
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

      $(document).on('click', '.btn-action-view', function() {
         $('#viewLoan').modal('show');
         var id = $(this).data('id');
         $('#loanID').html(id);
       });


    // datatable initialization
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
          var buttons = '<button class="btn btn-secondary btn-action-view" tooltip="view details" data-id="' + row.LoanID + '"><i class="fa fa-eye"></i></button> ';
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
