<?php
require_once '../classess/SystemSettings.php';
require_once '../classess/SessionHandler.php';

$settings = new SystemSettings();
$session = new CustomSessionHandler();
$timezone = $settings->getTimezone();
$websiteTitle = $settings->getWebsiteTitle();
$styles = $settings->getStyles();
$scripts = $settings->getScripts();
$sweetAlert = $settings->getSweetAlertInit();
$ajax = $settings->getAjaxInit();
$validate = $settings->validateForms();
date_default_timezone_set($timezone);


if($session->isSessionVariableSet("isLoggedin")){
  header("Location: ../pages/");
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sign up | <?php echo $websiteTitle; ?></title>
    <?php echo $styles; ?>
    <link href="../assets/css/auth.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="auth-content">
            <div class="card">
                <div class="card-body text-center">
                  <div class="mb-4">
                      <img class="brand" src="../assets/img/ms-icon-144x144.png" alt="<?php echo $websiteTitle; ?> logo">
                      <img class="brand" style="height: 144px;" src="../assets/img/poz-logo.png" alt="<?php echo $websiteTitle; ?> logo">
                  </div>
                  <h2>Library Management System</h2>
                    <h6 class="mb-4 text-muted">Create new account</h6>
                  <form action="" id="signupForm" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3 text-start">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="Name" id="Name" placeholder="Enter Name" required>
                            <div class="invalid-feedback">
                              Please enter your name.
                            </div>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" name="Email" id="Email" placeholder="Enter Email" required>
                            <div class="invalid-feedback">
                              Please provide a valid email address.
                            </div>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="name" class="form-label">Phone Number</label>
                            <sub class="text-muted"> e.g. 09123456789 </sub>
                            <input type="tel" class="form-control" name="Phone" id="Phone" placeholder="Enter Phone Number" pattern="[0-9]{11}" required>
                            <div class="invalid-feedback">
                              Please enter valid phone number.
                            </div>
                        </div>
                        <div class="mb-3 text-start">
                          <label for="password" class="form-label">Password</label>
                          <input type="password" class="form-control" name="Password" id="Password" pattern=".{8,}" placeholder="Password" required>
                          <div class="invalid-feedback password-error">
                            Please enter a password that is at least 8 characters long.
                          </div>
                        </div>
                        <div class="mb-3 text-start">
                          <input type="password" class="form-control"  id="ConfirmPassword" placeholder="Confirm password" required>
                          <div class="invalid-feedback confirm-password-error">
                            Please confirm your password.
                          </div>
                        </div>

                        <div class="mb-3 text-start">
                            <div class="form-check">
                              <input class="form-check-input" name="confirm" type="checkbox" value="" id="check1">
                              <label class="form-check-label" for="check1">
                                I agree to the <a href="#" tabindex="-1">terms and policy</a>.
                              </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary shadow-2 mb-4">Register</button>
                    </form>
                    <p class="mb-0 text-muted">Allready have an account? <a href="login.php">Log in</a></p>
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
      $('#signupForm').on('submit', function() {
        event.preventDefault();
        var password = $('#Password').val();
        var confirmPassword = $('#ConfirmPassword').val();

        // Check if password is at least 8 characters long
        if (password.length < 8) {
            $('#Password').addClass('is-invalid');
            $('.password-error').show();
            return false; // Prevent form submission
        } else {
            $('#Password').removeClass('is-invalid');
            $('.password-error').hide();
        }

        // Check if both password and confirm password fields are empty
        if (password === '' && confirmPassword === '') {
            $('#ConfirmPassword').addClass('is-invalid');
            $('.confirm-password-error').show();
            return false; // Prevent form submission
        }

        // Check if password and confirm password match
        if (password !== confirmPassword) {
            $('#ConfirmPassword').addClass('is-invalid');
            $('.confirm-password-error').show();
            return false; // Prevent form submission
        } else {
            $('#ConfirmPassword').removeClass('is-invalid');
            $('.confirm-password-error').hide();
        }

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
        loadContent('../controllers/signupController.php', formData, successCallback, errorCallback);

    });

});
    </script>

</body>

</html>
