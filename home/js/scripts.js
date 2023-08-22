/*!
* Start Bootstrap - Personal v1.0.1 (https://startbootstrap.com/template-overviews/personal)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-personal/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project


$(document).ready(function() {
      $(window).scroll(function() {
          if ($(this).scrollTop() > 200) {
              $('#back-to-top').fadeIn();
          } else {
              $('#back-to-top').fadeOut();
          }
      });

      $('#back-to-top').click(function() {
          $('html, body').animate({ scrollTop: 0 }, 'slow');
          return false;
      });
  });
</script>
