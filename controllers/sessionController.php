<?php
require_once '../classess/SessionHandler.php';

$session=new CustomSessionHandler();

if($session->checkSessionExpirationAsync()){
  $response = array("success" => true, "message" => "You are inactive for 10 minutes.");
  echo json_encode($response);
}else{
  $response = array("success" => false, "message" => "ok");
}

 ?>
