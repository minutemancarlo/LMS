<?php
require_once '../classess/SystemSettings.php';
$settings = new SystemSettings();
$settings->setDefaultTimezone();
$websiteTitle = $settings->getWebsiteTitle();
$styles = $settings->getStyles();
$scripts = $settings->getScripts();
$sweetAlert = $settings->getSweetAlertInit();
$ajax = $settings->getAjaxInit();
$validate = $settings->validateForms();
 ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Forgot Password | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
    <link href="../assets/css/auth.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="auth-content">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                      <img class="brand" style="height: 50%; width:100%" src="../assets/img/navbar-icon.png" alt="<?php echo $websiteTitle; ?> logo">
                    </div>
                    <h6 class="mb-4 text-muted">Reset Password</h6>
                    <p class="text-muted text-start">Enter your email address and your new password will be emailed to you.</p>
                    <form action="" id="resetPass" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3 text-start">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                            <div class="invalid-feedback">
                              Please provide a valid email address.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary shadow-2 mb-4">Send me new password</button>
                    </form>
                    <p class="mb-0 text-muted">Don’t have an account? <a href="signup.php">Sign up</a></p>
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

    $('#resetPass').on('submit', function() {
      event.preventDefault();
      var successCallback = function(response) {

          var data = JSON.parse(JSON.stringify(response));
        if (data.success) {
          Toast.fire({
            icon: 'success',
            title: data.message,
            timer: 2000,
          }).then(() => {
             window.location.href = window.origin;

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
      loadContent('../controllers/forgotpasswordController.php', formData, successCallback, errorCallback);
    });

  });
</script>
</body>

</html>
