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

 ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Settings | <?php echo $websiteTitle; ?></title>
    <link href="../assets/vendor/fontawesome/css/fontawesome.min.css" rel="stylesheet">
    <link href="../assets/vendor/fontawesome/css/solid.min.css" rel="stylesheet">
    <link href="../assets/vendor/fontawesome/css/brands.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
                                    <div class="col-md-6">
                                        <p class="text-muted">General settings such as, site title, site description, address and so on.</p>
                                        <div class="mb-3">
                                            <label for="site-title" class="form-label">Site Title</label>
                                            <input type="text" name="site_title" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label for="site-description" class="form-label">Site Description</label>
                                            <textarea class="form-control" name="site_description"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Site Logo</label>
                                              <input class="form-control" name="site_logo" type="file" id="formFile1">
                                            <small class="text-muted">The image must have a maximum size of 1MB</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Favicon</label>
                                              <input class="form-control" name="site_favicon" type="file" id="formFile2">
                                            <small class="text-muted">The image must have a maximum size of 1MB</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Google Analytics Code</label>
                                            <textarea class="form-control" name="google_analytics_code" rows="4"></textarea>
                                        </div>
                                        <div class="mb-3 text-end">
                                            <button class="btn btn-success" type="submit"><i class="fas fa-check"></i> Save</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                                    <div class="col-md-6">
                                        <p class="text-muted">Email SMTP settings, notifications and others related to email.</p>
                                        <!-- <div class="mb-3">
                                            <label for="" class="form-label">Protocol</label>
                                            <select name="" class="form-select">
                                                <option value="">Select Protocol</option>
                                                <option value="">SMTP</option>
                                                <option value="">Sendmail</option>
                                                <option value="">PHP Mailer</option>
                                            </select>
                                        </div> -->
                                        <div class="mb-3">
                                            <label for="" class="form-label">SMTP Host</label>
                                            <input type="text" name="" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label">SMTP Username</label>
                                            <input type="text" name="" class="form-control">
                                        </div>
                                        <!-- <div class="mb-3">
                                            <label for="" class="form-label">SMTP Security</label>
                                            <select name="" class="form-select">
                                                <option value="">Select SMTP Security</option>
                                                <option value="">TLS</option>
                                                <option value="">SSL</option>
                                                <option value="">None</option>
                                            </select>
                                        </div> -->
                                        <div class="mb-3">
                                            <label for="" class="form-label">SMTP Port</label>
                                            <input type="text" name="" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label">SMTP Password</label>
                                            <input type="password" name="" class="form-control">
                                        </div>
                                        <div class="mb-3 text-end">
                                            <button class="btn btn-primary" type="button"><i class="fas fa-paper-plane"></i> Test</button>
                                            <button class="btn btn-success" type="submit"><i class="fas fa-check"></i> Save</button>
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
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>
