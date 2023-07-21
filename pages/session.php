<?php
require_once '../classess/SessionHandler.php';

$session = new CustomSessionHandler();

if(!$session->isSessionVariableSet("Role")){
  header("Location: ../");
}

 ?>
