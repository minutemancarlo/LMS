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
<html lang="en" >

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Books | <?php echo $websiteTitle; ?></title>
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
                  <div class="container">
                      <div class="page-title">
                          <h3>Catalog Management
                              <a href="roles.html" class="btn btn-sm btn-outline-primary float-end"><i class="fas fa-user-shield"></i> Roles</a>
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
                                  <div class="tab-pane fade" id="catalogs" role="tabpanel" aria-labelledby="catalogs-tab">
                                    <h2>Genre Management</h2>
                                    <div class="table-responsive">
                                      <table width="100%" class="table table-hover" id="genreTable">

                                      </table>
                                    </div>
                                  </div>
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
        { title: 'Quantity', data: "Quantity", visible: true },
        { title: 'Remaining', data: "Remaining", visible: true },
        {
          title: 'Genre',
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
            buttons += '<button class="btn btn-danger btn-action-delete" data-id="' + row.BookID + '"><i class="fa fa-trash"></i></button> ';
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
