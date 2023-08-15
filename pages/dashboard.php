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

    <title>Dashboard | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
    <link href="../assets/css/master.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div id="body" class="active">
            <?php include 'navbar.php'; ?>
            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">Overview</div>
                            <h2 class="page-title">Dashboard</h2>
                            <!-- <div class="col-md-4 mb-3">
                              <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-filter"></i></span>
                                  <input type="text" class="datepicker-here form-control" data-range="true" data-multiple-dates-separator="-" data-language="en" placeholder="Filter" aria-label="Filter" aria-describedby="basic-addon1">
                                  <button class="btn btn-primary" type="button" name="filterBtn" id="button-addon1">Go!</button>
                              </div>
                            </div> -->
                        </div>
                    </div>
                    <?php echo $cards; ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="content">
                                            <div class="head">
                                                <h5 class="mb-0">Overdues Overview</h5>
                                                <p class="text-muted">Current number of overdue books</p>
                                            </div>
                                            <div class="canvas-wrapper">
                                                <canvas class="chart" id="trafficflow"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="content">
                                            <div class="head">
                                                <h5 class="mb-0">Circulations Overview</h5>
                                                <p class="text-muted">Current borrowed and returned books</p>
                                            </div>
                                            <div class="canvas-wrapper">
                                                <canvas class="chart" id="sales"></canvas>
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
    </div>
    <?php echo $scripts; ?>
    <script src="../assets/js/dashboard-charts.js"></script>
    <script src="../assets/js/script.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {

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
      var trafficchart = document.getElementById("trafficflow");


  $.ajax({
      url: '../controllers/dashboardController.php', // Replace with the actual URL of your PHP script
      type: 'POST',
      data: {
          action: 'overdue'
      },
      success: function(response) {
        console.log(response);
          var myChart1 = new Chart(trafficchart, {
              type: 'line',
              data: {
                  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                  datasets: [{
                      data: response, // Use the OverdueCount values from the response
                      backgroundColor: "rgba(48, 164, 255, 0.2)",
                      borderColor: "rgba(48, 164, 255, 0.8)",
                      fill: true,
                      borderWidth: 1
                  }]
              },
              options: {
                  animation: {
                      duration: 2000,
                      easing: 'easeOutQuart',
                  },
                  plugins: {
                      legend: {
                          display: false,
                          position: 'right',
                      },
                      title: {
                          display: true,
                          text: 'Number of Overdue Loans',
                          position: 'left',
                      },
                  },
              },
              scales: {
                  y: {
                      beginAtZero: true,
                      ticks: {
                          stepSize: 1,
                          callback: function(value, index, values) {
                              return value.toFixed(0);
                          }
                      }
                  }
              }
          });
      },
      error: function(xhr, status, error) {
          console.error(error);

      }
  });

  $.ajax({
       url: '../controllers/dashboardController.php', // Replace with your server endpoint
       type: 'POST',
       data: {
           action: 'circulations'
       },
       success: function(response) {
           // Create the Chart.js chart

           var saleschart = document.getElementById("sales");

           myChart2 = new Chart(saleschart, {
               type: 'bar',
               data: {
                   labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                   datasets: [{
                       label: 'Borrowed',
                       data: response.borrowed,
                       backgroundColor: "rgba(48, 164, 255, 0.2)",
                       borderColor: "rgba(48, 164, 255, 0.8)",
                       fill: true,
                       borderWidth: 1
                   },
                   {
                       label: 'Returned',
                       data: response.returned,
                       backgroundColor: "rgba(255, 99, 132, 0.2)",
                       borderColor: "rgba(255, 99, 132, 0.8)",
                       fill: true,
                       borderWidth: 1
                   }]
               },
               options: {
                   animation: {
                       duration: 2000,
                       easing: 'easeOutQuart',
                   },
                   scales: {
                       y: {
                           beginAtZero: true,
                           ticks: {
                               stepSize: 1,
                               callback: function(value, index, values) {
                                   return value.toFixed(0);
                               }
                           }
                       }
                   },
                   plugins: {
                       legend: {
                           display: true,
                           position: 'bottom'
                       },
                       title: {
                           display: true,
                           text: 'Circulations Count',
                           position: 'left',
                       },
                   },
               }
           });
       },
       error: function(xhr, status, error) {
           console.error(error);
       }
   });


    });
    </script>
</body>

</html>
