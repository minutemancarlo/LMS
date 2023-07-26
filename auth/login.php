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
  if ($session->getSessionVariable('Role')=='0') {
    header("Location: ../pages/");
  }
  header("Location: ../pages/books.php");
}

?>

<!doctype html>
<html lang="en">

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
    <title>Login | <?php echo $websiteTitle; ?></title>
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../assets/vendor/fontawesome/css/fontawesome.min.css" rel="stylesheet">
        <link href="../assets/vendor/fontawesome/css/solid.min.css" rel="stylesheet">
        <link href="../assets/vendor/fontawesome/css/brands.min.css" rel="stylesheet">
        <link href="../assets/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <link href="../assets/css/auth.css" rel="stylesheet">
    <style media="screen">
      #togglePassword {
        cursor: pointer;
      }
      #eyeIcon {
        font-size: 1.2rem;
      }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="auth-content">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <img class="brand" style="height: 50%; width:100%" src="../assets/img/navbar-icon.png" alt="<?php echo $websiteTitle; ?> logo">
                    </div>
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
                        <div class="input-group mb-3">
                          <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            <span class="input-group-text"  id="togglePassword"><i class="fa fa-eye-slash" id="eyeIcon"></i></span>
                        </div>
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
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
    function setCookie(name, value, days) {
      var expires = "";
      if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }
      $(document).ready(function() {
        <?php echo $sweetAlert; ?>
        <?php echo $ajax; ?>
        <?php echo $validate; ?>

        $('#loginForm').submit(function(event) {
          event.preventDefault();
          var rememberMe = $("#check1").is(":checked");
          if (rememberMe) {
              setCookie("rememberMe", "true", 7); // Expires in 7 days
              setCookie("Email", $('#email').val(), 7);
              setCookie("Password", $('#password').val(), 7);
            } else {
              // Remove the "Remember me" cookie if not checked
              setCookie("rememberMe", "", -1); // Expire the cookie immediately
              setCookie("Email", "", -1);
              setCookie("Password", "", -1);
            }
              var successCallback = function(response) {

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

          $("#togglePassword").on("click", function() {
           var passwordInput = $("#password");
           var eyeIcon = $("#eyeIcon");

           if (passwordInput.attr("type") === "password") {
               passwordInput.attr("type", "text");
               eyeIcon.removeClass("fa-eye-slash").addClass("fa-eye");
           } else {
               passwordInput.attr("type", "password");
               eyeIcon.removeClass("fa-eye").addClass("fa-eye-slash");
           }
       });

       var rememberMe = getCookie("rememberMe");
       if (rememberMe === "true") {
         // If the cookie is set, check the checkbox
         $("#check1").prop("checked", true);
         console.log(getCookie("Email"));
         $("#email").val(getCookie("Email"));
         $("#password").val(getCookie("Password"));

       }
});




      function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1, c.length);
          }
          if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length, c.length);
          }
        }
        return null;
      }
    </script>
</body>

</html>
