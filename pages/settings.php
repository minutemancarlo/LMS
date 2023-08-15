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
$websiteTitle=$config['website']['name'];
$analytics=$config['analytics']['token'];

$email_host = $config['email']['host'];
$email_port = $config['email']['port'];
$email_username = $config['email']['username'];
$email_password = $config['email']['password'];
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
    <title>Settings | <?php echo $websiteTitle; ?></title>
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
                    <div class="page-title">
                        <h3>Settings</h3>
                    </div>
                    <div class="box box-primary">
                        <div class="box-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="email-tab" data-bs-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="attributions-tab" data-bs-toggle="tab" href="#attributions" role="tab" aria-controls="attributions" aria-selected="false">Attributions</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade active show" id="general" role="tabpanel" aria-labelledby="general-tab">
                                  <form id="websiteSettings" enctype="multipart/form-data">
                                    <div class="col-md-6">
                                        <p class="text-muted">General settings such as, site title, and so on.</p>
                                        <div class="mb-3">
                                            <label for="site-title" class="form-label">Site Title</label>
                                            <input type="text" name="site_title" class="form-control" value="<?php echo $websiteTitle; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Favicon</label>
                                            <input class="form-control" name="site_favicon" type="file" id="formFile2" accept=".png">
                                            <small class="text-muted">The image must have a maximum size of 1MB</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Google Analytics Code</label>
                                            <textarea class="form-control" name="google_analytics_code"  rows="4"><?php echo $analytics; ?></textarea>
                                        </div>
                                        <div class="mb-3 text-end">
                                            <button class="btn btn-success" type="submit"><i class="fas fa-check"></i> Save</button>
                                        </div>
                                    </div>
                                  </form>

                                </div>
                                <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                                  <form id="emailSettings" method="post">
                                    <div class="col-md-6">
                                        <p class="text-muted">Email SMTP settings, notifications and others related to email.</p>
                                        <div class="mb-3">
                                            <label for="" class="form-label">SMTP Host</label>
                                            <input type="text" name="host" class="form-control" value="<?php echo $email_host; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label">SMTP Username</label>
                                            <input type="text" name="username" class="form-control" value="<?php echo $email_username; ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">SMTP Port</label>
                                            <input type="text" name="port" class="form-control" value="<?php echo $email_port; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label">SMTP Password</label>
                                            <input type="password" name="password" class="form-control" value="<?php echo $email_password; ?>">
                                        </div>
                                        <div class="mb-3 text-end">
                                            <!-- <button class="btn btn-primary" type="button"><i class="fas fa-paper-plane"></i> Test</button> -->
                                            <button type="submit" class="btn btn-success" type="submit"><i class="fas fa-check"></i> Save</button>
                                          </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="attributions" role="tabpanel" aria-labelledby="attributions-tab">
                                    <h4 class="mb-0">Legal Notice</h4>
                                    <p class="text-muted">Copyright (c) Library Management System. <?php echo date('Y'); ?>. All rights reserved.</p>
                                    <p class="mb-0"><strong>Bootstrap</strong></p>
                                    <p class="text-muted">The MIT License (MIT)</p>
                                    <p class="ps-4 col-md-6">
                                        Copyright 2011-2018 Twitter, Inc. <br><br>
                                        Permission is hereby granted, free of charge, to any person obtaining a copy
                                        of this software and associated documentation files (the "Software"), to deal
                                        in the Software without restriction, including without limitation the rights
                                        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
                                        copies of the Software, and to permit persons to whom the Software is
                                        furnished to do so, subject to the following conditions:
                                        <br><br>
                                        The above copyright notice and this permission notice shall be included in
                                        all copies or substantial portions of the Software.
                                        <br><br>
                                        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                                        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                                        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                                        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                                        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                                        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
                                        THE SOFTWARE.
                                    </p>
                                    <p class="mb-0"><strong>jQuery JavaScript Library</strong></p>
                                    <p class="text-muted">The MIT License (MIT)</p>
                                    <p class="ps-4 col-md-6">
                                        Copyright jQuery Foundation and other contributors <br><br>
                                        Permission is hereby granted, free of charge, to any person obtaining a copy
                                        of this software and associated documentation files (the "Software"), to deal
                                        in the Software without restriction, including without limitation the rights
                                        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
                                        copies of the Software, and to permit persons to whom the Software is
                                        furnished to do so, subject to the following conditions:
                                        <br><br>
                                        The above copyright notice and this permission notice shall be included in
                                        all copies or substantial portions of the Software.
                                        <br><br>
                                        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                                        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                                        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                                        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                                        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                                        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
                                        THE SOFTWARE.
                                    </p>
                                    <p class="mb-0"><strong>DataTables</strong></p>
                                    <p class="text-muted">The MIT License (MIT)</p>
                                    <p class="ps-4 col-md-6">
                                        Copyright 2008-2018 SpryMedia Ltd <br><br>
                                        Permission is hereby granted, free of charge, to any person obtaining a copy
                                        of this software and associated documentation files (the "Software"), to deal
                                        in the Software without restriction, including without limitation the rights
                                        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
                                        copies of the Software, and to permit persons to whom the Software is
                                        furnished to do so, subject to the following conditions:
                                        <br><br>
                                        The above copyright notice and this permission notice shall be included in
                                        all copies or substantial portions of the Software.
                                        <br><br>
                                        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                                        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                                        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                                        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                                        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                                        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
                                        THE SOFTWARE.
                                    </p>
                                    <p class="mb-0"><strong>Chart.js</strong></p>
                                    <p class="text-muted">The MIT License (MIT)</p>
                                    <p class="ps-4 col-md-6">
                                        Copyright 2018 Chart.js Contributors <br><br>
                                        Permission is hereby granted, free of charge, to any person obtaining a copy
                                        of this software and associated documentation files (the "Software"), to deal
                                        in the Software without restriction, including without limitation the rights
                                        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
                                        copies of the Software, and to permit persons to whom the Software is
                                        furnished to do so, subject to the following conditions:
                                        <br><br>
                                        The above copyright notice and this permission notice shall be included in
                                        all copies or substantial portions of the Software.
                                        <br><br>
                                        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                                        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                                        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                                        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                                        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                                        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
                                        THE SOFTWARE.
                                    </p>
                                    <p class="mb-0"><strong>Air Datepicker</strong></p>
                                    <p class="text-muted">The MIT License (MIT)</p>
                                    <p class="ps-4 col-md-6">
                                        Copyright (c) 2016 Timofey Marochkin <br><br>
                                        Permission is hereby granted, free of charge, to any person obtaining a copy
                                        of this software and associated documentation files (the "Software"), to deal
                                        in the Software without restriction, including without limitation the rights
                                        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
                                        copies of the Software, and to permit persons to whom the Software is
                                        furnished to do so, subject to the following conditions:
                                        <br><br>
                                        The above copyright notice and this permission notice shall be included in
                                        all copies or substantial portions of the Software.
                                        <br><br>
                                        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                                        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                                        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                                        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                                        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                                        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
                                        THE SOFTWARE.
                                    </p>
                                    <p class="mb-0"><strong>MDTimePicker</strong></p>
                                    <p class="text-muted">The MIT License (MIT)</p>
                                    <p class="ps-4 col-md-6">
                                        Copyright (c) 2017 Dionlee Uy <br><br>
                                        Permission is hereby granted, free of charge, to any person obtaining a copy
                                        of this software and associated documentation files (the "Software"), to deal
                                        in the Software without restriction, including without limitation the rights
                                        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
                                        copies of the Software, and to permit persons to whom the Software is
                                        furnished to do so, subject to the following conditions:
                                        <br><br>
                                        The above copyright notice and this permission notice shall be included in
                                        all copies or substantial portions of the Software.
                                        <br><br>
                                        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                                        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                                        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                                        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                                        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                                        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
                                        THE SOFTWARE.
                                    </p>
                                    <p class="mb-0"><strong>Fontawesome</strong></p>
                                    <p class="text-muted">The MIT License (MIT)</p>
                                    <p class="ps-4 col-md-6">
                                        Font Awesome Free License <br><br>
                                        Permission is hereby granted, free of charge, to any person obtaining a copy
                                        of this software and associated documentation files (the "Software"), to deal
                                        in the Software without restriction, including without limitation the rights
                                        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
                                        copies of the Software, and to permit persons to whom the Software is
                                        furnished to do so, subject to the following conditions:
                                        <br><br>
                                        The above copyright notice and this permission notice shall be included in
                                        all copies or substantial portions of the Software.
                                        <br><br>
                                        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                                        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                                        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                                        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                                        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                                        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
                                        THE SOFTWARE.
                                    </p>
                                    <p class="mb-0"><strong>Flag Icon CSS</strong></p>
                                    <p class="text-muted">The MIT License (MIT)</p>
                                    <p class="ps-4 col-md-6">
                                        Copyright (c) 2013 Panayiotis Lipiridis <br><br>
                                        Permission is hereby granted, free of charge, to any person obtaining a copy
                                        of this software and associated documentation files (the "Software"), to deal
                                        in the Software without restriction, including without limitation the rights
                                        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
                                        copies of the Software, and to permit persons to whom the Software is
                                        furnished to do so, subject to the following conditions:
                                        <br><br>
                                        The above copyright notice and this permission notice shall be included in
                                        all copies or substantial portions of the Software.
                                        <br><br>
                                        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                                        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                                        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                                        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                                        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                                        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
                                        THE SOFTWARE.
                                    </p>
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
      }, 10000);



$('#websiteSettings').submit(function(event) {
    event.preventDefault();


    var formData = new FormData(this);
    // Define success and error callbacks
var successCallback = function(response) {
  console.log(response);
  // Perform actions on successful response
  if (response.success) {
      Toast.fire({
          icon: 'success',
          title: response.message,
          timer: 2000,
      }).then(() => {
          location.reload();
          // You can also perform other actions after successful response
      });
  } else {
      Toast.fire({
          icon: 'error',
          title: response.message
      });
  }
};

var errorCallback = function(xhr, status, error) {
  console.error(error);
  // Handle error
  Toast.fire({
      icon: 'error',
      title: "Unexpected Error Occured. Please check browser logs for more info."
  });
};
$.ajax({
  url: '../controllers/settingsController.php',
  type: 'POST',
  data: formData,
  dataType: 'json',
  processData: false,
  contentType: false,
  success: successCallback,
  error: errorCallback
});


});

$('#emailSettings').submit(function(event) {
    event.preventDefault();



    // Define success and error callbacks
var successCallback = function(response) {
  console.log(response);
  // Perform actions on successful response
  if (response.success) {
      Toast.fire({
          icon: 'success',
          title: response.message,
          timer: 2000,
      }).then(() => {
          location.reload();
          // You can also perform other actions after successful response
      });
  } else {
      Toast.fire({
          icon: 'error',
          title: response.message
      });
  }
};

var errorCallback = function(xhr, status, error) {
  console.error(error);
  // Handle error
  Toast.fire({
      icon: 'error',
      title: "Unexpected Error Occured. Please check browser logs for more info."
  });
};
var formData = $(this).serialize();

// Use the loadContent function to send the form data
loadContent('../controllers/settingsController.php', formData, successCallback, errorCallback);


});


    });
    </script>
</body>

</html>
