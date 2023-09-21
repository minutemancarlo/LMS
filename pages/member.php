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
$roleName = $session->getSessionVariable("Name");
$menuTags = $roleHandler->getMenuTags($roleValue);
$result=$db->select('loan','*','is_returned=0');
$borrowed=$result->num_rows;
$overdue=0;
$result=$db->select('member','*','');
$users=$result->num_rows;
$result=$db->select('member','*','is_verified=0');
$unverified=$result->num_rows;
$cards = $roleHandler->getCards($roleValue,$borrowed,$overdue,$users,$unverified);
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

    <title>Members | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
    <link href="../assets/css/master.css" rel="stylesheet">
    <style media="screen">
    .inactive-row td {
        color: red;
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
                  <div class="container">
                      <div class="page-title">
                          <h3>Member Management
                              <!-- <a href="roles.html" class="btn btn-sm btn-outline-primary float-end"><i class="fas fa-user-shield"></i> Roles</a> -->
                          </h3>
                      </div>
                      <div class="box box-primary">
                          <div class="box-body">
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
    <div class="modal fade" id="editMember" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
           <div class="modal-dialog">
               <div class="modal-content">
                   <!-- Modal Header -->
                   <div class="modal-header">
                       <h5 class="modal-title" id="exampleModalLabel">Member ID: <strong id="member_id"></strong> </h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                   </div>
                   <!-- Modal Body -->
                   <div class="modal-body">
                     <form id="memberForm" method="POST">
                       <div class="mb-3">
                           <label for="memberName" class="form-label">Member Name</label>
                           <input type="text" name="memberID" id="memberID" value="" hidden readonly>
                           <input type="text" name="is_active" id="is_active" value="" hidden readonly>
                           <input type="text" class="form-control" id="memberName" name="memberName" readonly>
                       </div>
                       <div class="mb-3">
                           <label for="memberEmail" class="form-label">Member Email</label>
                           <input type="email" class="form-control" id="memberEmail" name="memberEmail" readonly>
                       </div>
                       <div class="mb-3">
                           <label for="memberPhone" class="form-label">Member Phone</label>
                           <input type="tel" class="form-control" id="memberPhone" name="memberPhone" readonly>
                       </div>
                       <div class="mb-3">
                           <label for="memberAddress" class="form-label">Member Address</label>
                           <textarea class="form-control" id="memberAddress" name="memberAddress" readonly></textarea>
                       </div>
                       <div class="mb-3">
                           <label for="memberRole" class="form-label">Role</label>
                           <select class="form-control" id="memberRole" name="memberRole" readonly>
                               <option value="0">Administrator</option>
                               <option value="1">Standard User</option>
                           </select>
                       </div>
                   </div>
                   <!-- Modal Footer -->
                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                       <button type="submit" class="btn btn-primary">Save changes</button>
                   </div>
                 </form>
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

      $('#memberForm').submit(function(event) {
              event.preventDefault();

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

              var formData = new FormData(this);
              formData.append("action", "update");
              formData.append("by", "<?php echo $roleName; ?>");

              $.ajax({
                  url: '../controllers/memberController.php',
                  type: 'POST',
                  data: formData,
                  dataType: 'json',
                  processData: false,
                  contentType: false,
                  success: successCallback,
                  error: errorCallback
              });
          });

        $(document).on('click', '.btn-action-edit', function() {
           $('#editMember').modal('show');

           var id = $(this).data('id');
           var row = table.row($(this).closest('tr')).data();

          $('#member_id').html(id);
          $('#memberID').val(id);
          $('#memberName').val(row.Name);
          $('#memberEmail').val(row.Email);
          $('#memberPhone').val(row.Phone);
          $('#memberAddress').val(row.Address);
          $('#memberRole').val(row.Role);
          $('#is_active').val(row.Is_active);

          $('#editMember').modal('show');
         });

         $(document).on('click', '.btn-action-delete', function() {

           var id = $(this).data('id');
           var row = table.row($(this).closest('tr')).data();
           var res = row.is_active==0?'Activation':'Deactivation';
           var btn = row.is_active==0?'Activate':'Deactivate';
           var st = row.is_active==0?1:0;
    // Show a SweetAlert2 confirmation dialog
    Swal.fire({
        title: 'Confirm '+ res,
        text: 'Are you sure you want to '+ btn +' this member?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: btn,
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {


          $('#member_id').html(id);
          $('#memberID').val(id);
          $('#memberName').val(row.Name);
          $('#memberEmail').val(row.Email);
          $('#memberPhone').val(row.Phone);
          $('#memberAddress').val(row.Address);
          $('#memberRole').val(row.Role);
          $('#is_active').val(st);
          $("#memberForm").submit();
        }
    });
});


           //  $('#editMember').modal('show');
           //

           //
           // $('#editMember').modal('show');
          // });

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
        var stat = data == 1 ? 'Verified' : 'Not Verified';
        return stat;
      }

          },
          {
            title: 'Role',
            data: "Role",
            className: "text-center",
            visible: true,
            render: function(data, type, row) {
        var stat = data == 1 ? 'Standard User' : 'Administrator';
        return stat;
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
              var buttons = '<a class="btn btn-success btn-action-edit" data-id="' + row.MemberID + '" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-pen"></i></a>';
              buttons += ' <button class="btn btn-danger btn-action-delete" data-id="' + row.MemberID + '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>';

              return buttons;
            }
          },
          { title: 'isactive', data: "is_active", visible: false },
        ],
        order: [[1, 'desc']],
    createdRow: function (row, data, dataIndex) {

        if (data.is_active == 0) {

            // If is_active is 0, add a class to make the text red
            $(row).addClass('inactive-row');
        }
    }
      });
    });
    </script>
</body>

</html>
