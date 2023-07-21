<?php
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';

$settings = new SystemSettings();
$session = new CustomSessionHandler();
$settings->setDefaultTimezone();
$websiteTitle = $settings->getWebsiteTitle();
$styles = $settings->getStyles();
$scripts = $settings->getScripts();
$sweetAlert = $settings->getSweetAlertInit();
$ajax = $settings->getAjaxInit();
$validate = $settings->validateForms();



if($session->isSessionVariableSet("Role")){
  header("Location: ../pages/");
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
    <link href="../assets/css/auth.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="auth-content">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <!-- <img class="brand" src="../assets/img/ms-icon-144x144.png" alt="<?php //echo $websiteTitle; ?> logo"> -->
                        <img class="brand" style="height: 120px; width:400px" src="../assets/img/navbar-icon.png" alt="<?php echo $websiteTitle; ?> logo">
                    </div>
                    <!-- <h2>Municipality of Pozorrubio</h2> -->
                    <h6 class="mb-4 text-muted">Login to your account</h6>
                    <form action="" id="loginForm" method="POST" class="needs-validation" novalidate>
                      <div class="mb-3 text-start">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" required>
                        <div class="invalid-feedback">
                          Please provide a valid email address.
                        </div>
                      </div>
                      <div class="mb-3 text-start">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        <div class="invalid-feedback">
                          Please enter your password.
                        </div>
                      </div>
                      <div class="mb-3 text-start">
                        <div class="form-check">
                          <input class="form-check-input" name="remember" type="checkbox" value="" id="check1">
                          <label class="form-check-label" for="check1">
                            Remember me on this device
                          </label>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary shadow-2 mb-4">Login</button>
                    </form>
                    <p class="mb-2 text-muted">Forgot password? <a href="forgot-password.php">Reset</a></p>
                    <p class="mb-0 text-muted">Don't have account yet? <a href="signup.php">Signup</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php echo $scripts; ?>
    <script type="text/javascript">
      $(document).ready(function() {
        <?php echo $sweetAlert; ?>
        <?php echo $ajax; ?>
        <?php echo $validate; ?>

        $('#loginForm').submit(function(event) {
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
               var formData = $(this).serialize();
              loadContent('../controllers/loginController.php', formData, successCallback, errorCallback);
          });
      });

    </script>
</body>

</html>
