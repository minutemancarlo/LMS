<?php
class SystemSettings {
    private $timezone;
    private $websiteTitle;
    private $scripts;
    private $styles;
    private $sweetAlert;
    private $ajax;
    private $validate;

    public function __construct() {
        $this->loadSettings();
    }

    private function loadSettings() {
        $this->timezone = 'America/New_York';
        $this->websiteTitle = 'LMS';
        $this->styles = '
        <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../assets/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
        ';
        $this->scripts = '
        <script src="../assets/vendor/jquery/jquery.min.js"></script>
        <script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="../assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
        ';
        $this->sweetAlert = "
        const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      });";

      $this->ajax = "
      function loadContent(url, data, successCallback, errorCallback) {
        $.ajax({
          url: url,
          method: 'POST',
          dataType: 'json',
          data: data,
          success: successCallback,
          error: errorCallback
        });
      }";

        $this->validate = "(function() {
            'use strict';
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation');
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();";
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function getWebsiteTitle() {
        return $this->websiteTitle;
    }

    public function getScripts() {
        return $this->scripts;
    }

    public function getStyles() {
        return $this->styles;
    }

    public function getSweetAlertInit() {
        return $this->sweetAlert;
    }

    public function getAjaxInit() {
        return $this->ajax;
    }

    public function validateForms() {
        return $this->validate;
    }
}
