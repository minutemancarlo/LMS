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
                      <!-- <img class="brand" src="../assets/img/ms-icon-144x144.png" alt="<?php //echo $websiteTitle; ?> logo">
                      <img class="brand" style="height: 144px;" src="../assets/img/poz-logo.png" alt="<?php //echo $websiteTitle; ?> logo"> -->
                      <img class="brand" style="height: 120px; width:400px" src="../assets/img/navbar-icon.png" alt="<?php echo $websiteTitle; ?> logo">

                  </div>
                  <!-- <h2>Library Management System</h2> -->
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
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" name="Address" id="Address" placeholder="Enter Address" required>
                            <div class="invalid-feedback">
                              Please provide a your Address.
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
                              <input class="form-check-input"  type="checkbox" value="" id="check1">
                              <label class="form-check-label" for="check1">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" tabindex="-1">terms and conditions</a>.
                              </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary shadow-2 mb-4">Register</button>
                    </form>
                    <p class="mb-0 text-muted">Already have an account? <a href="login.php">Log in</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>
          Welcome to our Library Management System! Before you proceed to use our services, we kindly request you to read and understand the following terms and conditions. By accessing or using our system, you agree to comply with these terms and conditions.
</br>
</br><strong>1. Privacy and Data Collection:</strong>
</br>1.1. To access our Library Management System, users will be required to provide certain personal information, including but not limited to name, email, phone number, and address.
</br>1.2. We will collect and process this information in accordance with our Privacy Policy, which can be found on our website. Your data will be handled securely and only used for the purpose of managing library-related activities.
</br>1.3. We will not sell, trade, or share your personal information with any third parties, except as required by law or with your explicit consent.
</br>
</br><strong>2. Account Responsibility:</strong>
</br>2.1. Users are responsible for maintaining the confidentiality of their account information, including login credentials (username and password).
</br>2.2. You agree to notify us immediately of any unauthorized use of your account or any other security breach.
</br>
</br><strong>3. Use of Library Materials:</strong>
</br>3.1. The Library Management System provides access to a variety of materials, including books, e-books, audio, and video content, subject to availability.
</br>3.3. Any misuse, unauthorized distribution, or copyright infringement may result in the termination of library privileges and potential legal consequences.

</br><strong>4. Conduct:</strong>
</br>4.1. Users are expected to maintain respectful and appropriate behavior while using the Library Management System.
</br>4.2. Any form of harassment, offensive content, or disruptive actions will not be tolerated and may lead to suspension or termination of library privileges.
</br>
</br><strong>5. System Availability:</strong>
</br>5.1. We will make reasonable efforts to ensure the continuous and uninterrupted availability of our Library Management System. However, we cannot guarantee that the system will be free from interruptions or technical issues.
</br>5.2. We reserve the right to temporarily suspend access to the system for maintenance, upgrades, or other necessary reasons.
</br>
</br><strong>6. Modifications to the Terms and Conditions:</strong>
</br>6.1. We may update or modify these terms and conditions from time to time. Users will be notified of any significant changes, and continued use of the Library Management System constitutes acceptance of the revised terms.
</br>
</br><strong>7. Termination of Services:</strong>
</br>7.1. We reserve the right to terminate or suspend access to the Library Management System at any time and for any reason without prior notice.
</br>7.2. Upon termination, users will lose access to all library-related services and materials.
</br>
</br><strong>8. Governing Law:</strong>
</br>8.1. These terms and conditions shall be governed by and construed in accordance with the laws of [your jurisdiction], without regard to its conflicts of law principles.
</br>
By using our Library Management System, you acknowledge that you have read, understood, and agreed to abide by these terms and conditions. If you do not agree to these terms, please refrain from using our services. If you have any questions or concerns, feel free to contact our customer support. Happy reading!
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

        //Check for the terms and condition if checked.

        if(!$('#check1').prop("checked")){
          Toast.fire({
          icon: 'error',
          title: "Please read and check terms and conditions."
        });
          return false;
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
