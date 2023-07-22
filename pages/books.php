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
$validate = $settings->validateForms();

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
<html lang="en" >

<head>
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K5W94QCNM0"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-K5W94QCNM0');
</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Books | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
    <link href="../assets/css/master.css" rel="stylesheet">
    <style>
      /* Optional CSS for styling tags */
      .tag {
        display: inline-block;
        padding: 5px;
        margin: 5px;
        background-color: #f1f1f1;
        border-radius: 5px;
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
                          <h3>Catalog Management
                              <a href="" id="bookModalbtn" data-bs-toggle="modal" data-bs-target="#bookModal" class="btn btn-sm btn-outline-primary float-end"><i class="fas fa-book-medical"></i> Add Book</a>
                          </h3>
                      </div>
                      <div class="box box-primary">
                          <div class="box-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                  <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="books-tab" data-bs-toggle="tab" href="#books" role="tab" aria-controls="books" aria-selected="true">Books</a>
                                  </li>
                                  <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="catalogs-tab" data-bs-toggle="tab" href="#catalogs" role="tab" aria-controls="catalogs" aria-selected="false">Genre</a>
                                  </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                  <!-- Books Tab Content -->
                                  <div class="tab-pane fade show active" id="books" role="tabpanel" aria-labelledby="books-tab">

                                    <div class="table-responsive">
                                      <table width="100%" class="table table-hover" id="booksTable">

                                      </table>
                                    </div>
                                  </div>
                                  <!-- Catalogs Tab Content -->
                                  <!-- <div class="tab-pane fade" id="catalogs" role="tabpanel" aria-labelledby="catalogs-tab">
                                    <h2>Genre Management</h2>
                                    <div class="table-responsive">
                                      <table width="100%" class="table table-hover" id="genreTable">

                                      </table>
                                    </div>
                                  </div> -->
                                </div>
                          </div>
                      </div>
                  </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODALS   -->
    <div class="modal fade" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="bookModalLabel">Add Book Information</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Book Form -->
            <form id="bookForm" novalidate method="POST" class="needs-validation" enctype="multipart/form-data">
              <input type="text" name="bookID" id="bookID" value="0" readonly hidden>
              <img src="../assets/img/book.png" id="thumbnailPreview"  alt="Thumbnail Preview" class="img-fluid rounded" style="width: 100px; height: 100px;">
              <div class="mb-3">
                <label for="thumbnail" class="form-label">Thumbnail Image</label>
                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                <div class="invalid-feedback">
                  Please select a thumbnail image.
                </div>
              </div>
              <div class="mb-3">
                <label for="bookTitle" class="form-label">Book Title</label>
                <input type="text" class="form-control" id="bookTitle" name="bookTitle" required>
                <div class="invalid-feedback">
                  Please fill out this field.
                </div>
              </div>
              <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" id="author" name="author" required>
                <div class="invalid-feedback">
                  Please fill out this field.
                </div>
              </div>
              <div class="mb-3">
                <label for="publication" class="form-label">Publication</label>
                <input type="text" class="form-control" id="publication" name="publication" required>
                <div class="invalid-feedback">
                  Please fill out this field.
                </div>
              </div>
              <div class="mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" required>
                <div class="invalid-feedback">
                  Please fill out this field.
                </div>
              </div>
              <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
                <div class="invalid-feedback">
                  Please fill out this field.
                </div>
              </div>
              <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <textarea class="form-control" id="genre" name="genre" placeholder="Place a comma to separate multiple genre tags" required></textarea>
                <div class="invalid-feedback">
                  Please add at least 1 genre.
                </div>
              </div>
              <div id="tagContainer"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="btnDelete" class="btn btn-danger">Delete</button>
                <button type="submit" id="btnBookSubmit" class="btn btn-primary">Add</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>









    <!-- MODALS -->



    <?php echo $scripts; ?>
    <script src="../assets/js/script.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      <?php echo $sweetAlert; ?>
      <?php echo $ajax; ?>
      <?php echo $validate; ?>
      var remaining=0;
      var quantity=0;
      var bookstable=$('#booksTable').DataTable({
      processing: true,
      ajax: {
        url: "../controllers/catalogController.php",
        type: "POST",
        data: { action: 'select', item: 'books'},
        dataType: 'json',
        dataSrc: '',
        cache: false
      },
      columns: [
        { title: 'BookID', data: "BookID", visible: false },
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
        { title: 'ISBN', data: "ISBN", visible: true },
        { title: 'Quantity', data: "Quantity", visible: true,
        className: 'text-center',
          render: function (data, type, row) {
            return '<span class="badge bg-primary">' + data + '</span> ';
          }
       },
        { title: 'Remaining', data: "Remaining", visible: true,
        className: 'text-center',
          render: function (data, type, row) {
            return '<span class="badge bg-primary">' + data + '</span> ';
          }
       },
        {
          title: 'Tags',
          data: "Genre",
          className: "text-center text-wrap",
          visible: true,
          render: function (data, type, row) {
      var genres = JSON.parse(data);
      var badges = '';
      for (var i = 0; i < genres.length; i++) {
        badges += '<span class="badge bg-secondary">' + genres[i] + '</span> ';
      }
      return badges;
    }
        },
        {
          title: 'Action',
          data: null,
          orderable: false,
          searchable: false,
          className: "text-center",
          render: function (data, type, row) {
            var buttons = '<button class="btn btn-success btn-action-edit" data-id="' + row.BookID + '"><i class="fa fa-edit"></i></button> ';
            return buttons;
          }
        }
      ],
      order: [[1, 'desc']]
    });



    $('#bookForm').submit(function(event) {
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
                // window.location.href = window.origin+'/lms/admin';
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
           // var formData = $(this).serialize();
           // Create a new FormData object
           var formData = new FormData(this);


           // Add the additional post variable and value to the formData object
            formData.append("action", "insert");
            $.ajax({
              url: '../controllers/catalogController.php',
              type: 'POST',
              data: formData,
              dataType: 'json',
              processData: false,
              contentType: false,
              success: successCallback,
              error: errorCallback
            });
            // loadContent('../controllers/catalogController.php', formData, successCallback, errorCallback);
      });


      $('#bookModal').on('hidden.bs.modal', function () {
        $('#bookForm')[0].reset();
        $('#thumbnailPreview').attr('src', '../assets/img/book.png');
        $('#bookForm .invalid-feedback .is-invalid').removeClass('is-invalid');
      });

      $(document).on('click', '#bookModalbtn', function () {
        $('#btnBookSubmit').html("Add");
        $('#bookModalLabel').html('Add Book Information');
        $('#tagContainer').html('');
        $('#thumbnail').val('');
        $('#btnDelete').prop('hidden',true);
      });


      $(document).on('click', '#btnDelete', function () {
        var bookID=$('#bookID').val();
        if (remaining==quantity) {
          Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to delete this book!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
          }).then((result) => {
            if (result.isConfirmed) {

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
              var formData = new FormData();
              formData.append("action", "delete");
              formData.append("bookID", bookID);
              $.ajax({
                url: '../controllers/catalogController.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false, // Important: Prevent jQuery from processing the data
                contentType: false, // Important: Prevent jQuery from setting content type
                success: successCallback,
                error: errorCallback
              });
            }
          });
        }else{
          setTimeout(function () {
          Swal.fire({
            title: 'Warning!',
            text: 'Some of this book is not yet returned.',
            icon: 'warning',
          });
        }, 1000);
        }
      });

      $(document).on('click', '.btn-action-edit', function () {
        // Get the row data using DataTables API
        var table = $('#booksTable').DataTable();
        var rowData = table.row($(this).closest('tr')).data();
        $('#bookForm')[0].reset();
        $('#btnBookSubmit').html("Update");
        $('#bookModalLabel').html('Update Book Information');
        $('#thumbnailPreview').attr('src', rowData.Thumbnail);
        $('#bookID').val(rowData.BookID);
        $('#bookTitle').val(rowData.Title);
        $('#author').val(rowData.Author);
        $('#publication').val(rowData.Publication);
        $('#isbn').val(rowData.ISBN);
        $('#quantity').val(rowData.Quantity);
        $('#genre').val(JSON.parse(rowData.Genre));
        displayTags(JSON.parse(rowData.Genre));
        remaining=rowData.Remaining;
        quantity=rowData.Quantity;
        $('#btnDelete').prop('hidden',false);
        $('#bookModal').modal('show');
        // console.log(rowData);
      });


    });

    const tagTextArea = document.getElementById('genre');
    const tagContainer = document.getElementById('tagContainer');

    tagTextArea.addEventListener('keyup', function(event) {
      const tags = getTagsFromTextArea(event.target.value);
      displayTags(tags);
    });

    function getTagsFromTextArea(text) {
      const tagsArray = text.split(',').map(tag => tag.trim());
      return tagsArray.filter(tag => tag !== '');
    }

    function displayTags(tags) {
      tagContainer.innerHTML = '';
      tags.forEach(tag => {
        const tagElement = document.createElement('span');
        tagElement.textContent = tag;
        tagElement.classList.add('tag');
        tagContainer.appendChild(tagElement);
      });
    }

    // Function to display the selected image as a preview
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#thumbnailPreview').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }
  }

  // Trigger the readURL function when a thumbnail image is selected
  $('#thumbnail').change(function() {
    readURL(this);
  });


    </script>
</body>

</html>
