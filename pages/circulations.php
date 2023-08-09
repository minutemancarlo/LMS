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
                                        <select class="form-control" name="searchStatus">
                                          <option value="" selected>--Status--</option>
                                          <option value="" >All</option>
                                          <option value="">Borrowed</option>
                                          <option value="">Pending</option>
                                          <option value="">Overdue</option>
                                          <option value="">Returned</option>
                                        </select>
                                      </div>
                                      <div class="col">
                                        <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en" name="searchBorrowedDate" placeholder="Borrowed Date">
                                      </div>
                                      <div class="col">
                                        <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en" name="searchReturnDate" placeholder="Return Date">
                                      </div>
                                      <div class="col">
                                        <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en" name="searchDateReturned" placeholder="Date Returned">
                                      </div>
                                      <div class="col">
                                        <input type="text" class="form-control" value="" name="searchID" placeholder="Loan ID">
                                      </div>
                                      <div class="col">
                                        <button type="button" class="btn btn-primary" style="width: 100%" name="button"> <i class="fa fa-search"></i> Search</button>
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
                       <button type="button" id="release" class="btn btn-primary">Release</button>
                       <button type="button" id="notify" class="btn btn-warning">Notify</button>
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




    // datatable initialization
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

        if (new Date(row.DueDate) <= today) {
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
        var buttons = '<button class="btn btn-primary btn-action-view" tooltip="view details" data-id="' + row.LoanID + '"><i class="fa fa-eye"></i></button> ';
        return buttons;
      }
    }
  ],
  order: [[1, 'desc']]
});

$(document).on('click', '.btn-action-view', function() {
   $('#viewLoan').modal('show');
   var id = $(this).data('id');
   $('#loanID').html(id);
   cartTable.search(id).draw();

   var selectedRow = table.row($(this).closest('tr'));
  var today = new Date();
   if(selectedRow.data().status==='0'){
     $('#release').attr('hidden',false);
     $('#notify').attr('hidden',true);
   }else if(selectedRow.data().status==='1'){
     if (new Date(selectedRow.data().DueDate) <= today) {
       $('#release').attr('hidden',true);
       $('#notify').attr('hidden',false);
     } else {
       $('#release').attr('hidden',true);
       $('#notify').attr('hidden',true);
     }
   }else{
     $('#release').prop('hidden',true);
     $('#notify').prop('hidden',true);
   }
 });

 $('#release').click(function() {
   var successCallback = function(response) {
     console.log(response);
       var data = JSON.parse(JSON.stringify(response));
     if (data.success) {
       Toast.fire({
         icon: 'success',
         title: data.message,
         timer: 2000,
       }).then(() => {
         location.reload();
         // window.location.href = window.origin+'/lms/admin';
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
    var formData = { action: 'changeStatus', loanID:  $('#loanID').html(), status: 1};
   loadContent('../controllers/circulationsController.php', formData, successCallback, errorCallback);
});



  });
    </script>
</body>

</html>
