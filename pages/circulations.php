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
                                <span class="badge bg-secondary">Cancelled</span>
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
                                        <select class="form-control" id="searchStatus">
                                          <option value="" selected>--Status--</option>
                                          <option value="All" >All</option>
                                          <option value="Borrowed">Borrowed</option>
                                          <option value="Pending">Pending</option>
                                          <option value="Overdue">Overdue</option>
                                          <option value="Returned">Returned</option>
                                          <option value="Cancelled">Cancelled</option>
                                        </select>
                                      </div>
                                      <div class="col">
                                        <input type="text" class="datepicker-here form-control" data-date-format="yyyy-mm-dd" data-range="true" data-multiple-dates-separator="/" data-language="en" id="searchBorrowedDate" placeholder="Borrowed Date">
                                      </div>
                                      <div class="col">
                                        <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en" id="searchReturnDate" placeholder="Return Date">
                                      </div>
                                      <div class="col">
                                        <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en" id="searchDateReturned" placeholder="Date Returned">
                                      </div>
                                      <div class="col">
                                        <input type="text" class="form-control" value="" id="searchID" placeholder="Loan ID">
                                      </div>
                                      <div class="col">
                                        <button type="button" class="btn btn-primary" id="searchBtn" style="width: 100%" name="button">  Search</button>

                                      </div>
                                      <div class="col">

                                        <button type="button" class="btn btn-danger" id="clearSearch" style="width: 100%" name="button" onclick="location.reload();"> <i class="fa fa-sync"></i></button>
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
                       <button type="button" id="cancel" class="btn btn-danger">Cancel</button>
                       <button type="button" id="return" class="btn btn-info">Return</button>
                       <button type="button" id="notify" class="btn btn-warning">Extend</button>
                   </div>
               </div>
           </div>
       </div>


    <!-- MODALS -->
    <?php echo $scripts; ?>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>
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
      { title: 'ID', data: "LoanID", visible: false },

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
  },  <?php
    if ($roleValue=='0') {
      echo <<<HTML

  dom: 'Bfrtip',
  buttons: [
      {
          extend: 'excel',
          text: '<i class="fas fa-file-excel"></i> Export to Excel',
          className: 'btn btn-primary',
          filename: 'Circulationlist_' + new Date().toISOString().slice(0, 19).replace(/-/g, "_").replace(/:/g, "_"), // Set the filename with the current datetime
          exportOptions: {
              columns: [0,2,3,4,5,6], // Hide the 2nd column when exporting to Excel
          },
      },
      {
          extend: 'pdf',
          text: '<i class="fas fa-file-pdf"></i> Export to PDF',
          className: 'btn btn-danger',
          filename: 'Circulationlist_' + new Date().toISOString().slice(0, 19).replace(/-/g, "_").replace(/:/g, "_"), // Set the filename with the current datetime
          exportOptions: {
              columns: [0,2,3,4,5,6], // Hide the 2nd column when exporting to PDF
          },
          customize: function(doc) {
              // Set landscape mode for the PDF
              doc.content[1].layout = 'landscape';

              // Add a custom title in the header
              doc.content.unshift({
                  text: 'Circulations List as of ' + new Date().toLocaleDateString(),
                  fontSize: 18,
                  margin: [0, 0, 0, 10],
                  alignment: 'center'
              });
          }
      },
  ],
HTML;
    }

     ?>
     autoWidth: false,
  columns: [
    { title: 'ID', data: "LoanID", visible: true },
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
      }else if (data === '4') {
        badge = '<span class="badge bg-secondary">Cancelled</span> ';
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
  order: [[3, 'desc']]
});



$('#searchBtn').click(function() {
var searchStatus = $('#searchStatus').val();
table.columns(6).search(searchStatus).draw();

var searchBorrowedDate = $('#searchBorrowedDate').val();
var searchReturnDate = $('#searchReturnDate').val();
var searchDateReturned = $('#searchDateReturned').val();
var searchID = $('#searchID').val();

function getDateRange(dateRange) {
    if (!dateRange) {
        return null;
    }
    var dates = dateRange.split('/');
    return {
        startDate: dates[0].trim(),
        endDate: dates[1].trim()
    };
}

var borrowedDateRange = getDateRange(searchBorrowedDate);
var returnDateRange = getDateRange(searchReturnDate);
var dateReturnedRange = getDateRange(searchDateReturned);

$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    var borrowedDate = data[3];
    var returnDate = data[4];
    var dateReturned = data[5];

    function isDateInRange(date, range) {
        if (!range || !range.startDate || !range.endDate) {
            return true;
        }

        var dateObj = new Date(date);
        var startDateObj = new Date(range.startDate);
        var endDateObj = new Date(range.endDate);

        return dateObj >= startDateObj && dateObj <= endDateObj;
    }

    return (
        isDateInRange(borrowedDate, borrowedDateRange) &&
        isDateInRange(returnDate, returnDateRange) &&
        isDateInRange(dateReturned, dateReturnedRange) &&
        (searchID === '' || data[0] == searchID)
    );
});

table.draw();

$.fn.dataTable.ext.search.pop();
});



$(document).on('click', '.btn-action-view', function() {
   $('#viewLoan').modal('show');
   var id = $(this).data('id');
   $('#loanID').html(id);
   cartTable.columns(0).search(id).draw();

   var selectedRow = table.row($(this).closest('tr'));
  var today = new Date();
   if(selectedRow.data().status==='0'){
     $('#release').attr('hidden',false);
     $('#cancel').attr('hidden',false);
     $('#notify').attr('hidden',true);
     $('#return').attr('hidden',true);
   }else if(selectedRow.data().status==='1'){
     if (new Date(selectedRow.data().DueDate) <= today) {
       $('#release').attr('hidden',true);
       $('#cancel').attr('hidden',true);
       $('#return').attr('hidden',false);
       $('#notify').attr('hidden',false);
     } else {
       $('#release').attr('hidden',true);
       $('#cancel').attr('hidden',true);
       $('#notify').attr('hidden',true);
       $('#return').attr('hidden',false);
     }
   }else{
     $('#release').prop('hidden',true);
     $('#notify').prop('hidden',true);
     $('#return').attr('hidden',true);
     $('#cancel').attr('hidden',true);
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

$('#cancel').click(function() {
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
   var formData = { action: 'changeStatus', loanID:  $('#loanID').html(), status: 4 };
  loadContent('../controllers/circulationsController.php', formData, successCallback, errorCallback);
});


$('#return').click(function() {
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
   var formData = { action: 'changeStatus', loanID:  $('#loanID').html(), status: 2};
  loadContent('../controllers/circulationsController.php', formData, successCallback, errorCallback);
});

$('#notify').click(function() {
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
   var formData = { action: 'changeStatus', loanID:  $('#loanID').html(), extend: true, status: 2 };
  loadContent('../controllers/circulationsController.php', formData, successCallback, errorCallback);
});



  });
    </script>
</body>

</html>
