<?php

require '../checklogin.php';

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}

session_start();

if(!empty($_POST)) {
  unset($_POST['search']);
  try {
      $isExistingUser = $_POST['userid'];
      if(!empty($isExistingUser)) {
        unset($_POST['userid']);
        $newUser = $mgmt_api->users->update($isExistingUser,$_POST);
        $redirect = $_SERVER['HTTP_REFERER'];
      } else {
        $newUser = $mgmt_api->users->create($_POST);
        $redirect = '/admin/users.php';
      }
      $_SESSION["success"] = 'saved';
      header('Location: '.$redirect);
  } catch (Exception $e) {
      $_SESSION["error"] = $e->getMessage();
      header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
}
