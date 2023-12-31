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

    <title>Updates | <?php echo $websiteTitle; ?></title>
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

                </div>
                <div class="row">
                  <div class="col-md-2"></div>
                  <div class="col-md-8">
                    <div class="page-title">
                        System Updates
                    </div>
                    <div class="box box-primary" >

                        <div class="box-body" style="height: 100%">
                          <?php
                          require '../guzzle/vendor/autoload.php';

                          $username = 'minutemancarlo';
                          $repository = 'lms';

                          $accessToken = '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';

                          // API URL to retrieve all commits in the repository.
                          $apiUrl = "https://api.github.com/repos/{$username}/{$repository}/commits";

                          $headers = [
                              'Authorization' => "token {$accessToken}",
                              'Accept' => 'application/vnd.github.v3+json',
                          ];

                          $httpClient = new GuzzleHttp\Client();

                          try {

                              $commits = [];
                              $page = 1;
                              do {
                                  $response = $httpClient->request('GET', $apiUrl . '?page=' . $page, ['headers' => $headers]);
                                  $commitsData = json_decode($response->getBody(), true);
                                  $commits = array_merge($commits, $commitsData);
                                  $page++;
                              } while (!empty($commitsData));

                              // Display the commit information.
                              foreach ($commits as $commit) {
                                  $commitSha = $commit['sha'];
                                  $commitMessage = $commit['commit']['message'];
                                  $commitAuthor = $commit['commit']['author']['name'];
                                  $commitDate = $commit['commit']['author']['date'];
                                  $commitTitle = strtok($commitMessage, "\n");
                                 $commitDescription = trim(substr($commitMessage, strlen($commitTitle)));
                                 $commitDescription = !empty($commitDescription)?$commitDescription: "<i>(No description)</i>";
                                  $commitDate = new DateTime($commitDate);


                                  $formattedDate = $commitDate->format('F j, Y g:i A');
                                  echo '<div class="card">
                                    <div class="card-header">
                                      <strong>Commit ID: </strong> <i>'.$commitSha.PHP_EOL.'</i>';

                                      echo'
                                    </div>
                                    <div class="card-body">
                                      <strong>Title: </strong> '.$commitTitle. PHP_EOL;
                                      echo '<br><br><strong>Description</strong> <br>'.$commitDescription. PHP_EOL;
                                      echo '
                                    </div>
                                    <div class="card-footer">
                                      <strong>Updated On: </strong>'.$formattedDate . PHP_EOL;
                                      echo '
                                    </div>
                                  </div><br><hr><br>';


                              }
                          } catch (GuzzleHttp\Exception\RequestException $e) {
                              echo "Error: {$e->getMessage()}" . PHP_EOL;
                          }

                           ?>




                        </div>
                    </div>
                  </div>
                  <div class="col-md-2"></div>
                </div>

            </div>
        </div>
    </div>
    <?php echo $scripts; ?>
    <script src="../assets/js/script.js"></script>
    <script>
    <?php echo $sweetAlert; ?>
    <?php echo $ajax; ?>
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
    });
    </script>
</body>

</html>
