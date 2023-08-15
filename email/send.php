<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require 'phpmailer/vendor/autoload.php';
require_once '../classess/SystemSettings.php';
$settings = new SystemSettings();
$config = parse_ini_file('../config.ini', true);

// Access email settings
$emailHost = $config['email']['host'];
$emailUsername = $config['email']['username'];
$emailPort = $config['email']['port'];
$emailPassword = $config['email']['password'];



$email = $_POST['email'];
$subject = $_POST['subject'];
$link = $_POST['link'];
$name= $_POST['name'];
$password=isset($_POST['password'])?$_POST['password']:'';
$action=$_POST['action'];





    //Create a new PHPMailer instance
    $mail = new PHPMailer();

    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->SMTPDebug = 0;
    $mail->IsSMTP();
    $mail->Host = $emailHost;
    $mail->Port = $emailPort;//465

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->SMTPAuth = true;
    $mail->Username = $emailUsername;//'noreply@dev.x10.bz';
    $mail->Password = $emailPassword;//'2JJzkKnfLW';


    //Set who the message is to be sent from
    $mail->setFrom($emailUsername, "LMS Pozorrubio");
    //Set an alternative reply-to address
    $mail->addReplyTo($emailUsername, "LMS Pozorrubio");
    //Set who the message is to be sent to
    $mail->addAddress($email);

    //Set the subject line
    $mail->Subject = $subject;

    ob_start();
    switch ($action) {
      case 'register':
        include 'verify.php';
        break;

      case 'resetpassword':
        include 'password_reset_template.php';
        break;
    }



    $myvar = ob_get_clean();
    $mailContent = $myvar;
    $mail->Body = $mailContent;

    $mail->AltBody = 'This is a plain-text message body';
    //Attach an image file
    // $mail->addAttachment('../mail/images/phpmailer.png');

    if (!$mail->send()) {
      $logMessage=$subject." could not be sent to ".$email.". Info: ".$mail->ErrorInfo;
      $settings->createLogFile("EmailApi", $logMessage);
        // $response = array(
        //     'status' => 'error',
        //     'message' => 'Email could not be sent'
        //     // 'error' => $mail->ErrorInfo
        // );
    } else {
      $logMessage=$subject." sent to ".$email;
      $settings->createLogFile("EmailApi", $logMessage);
  // $response = array(
  //     'status' => 'success',
  //     'message' => 'Email sent successfully'
  // );
    }


// Convert the response to JSON
// $jsonResponse = json_encode($response);

// Set the JSON response headers
// header('Content-Type: application/json');
// echo $jsonResponse;


?>
