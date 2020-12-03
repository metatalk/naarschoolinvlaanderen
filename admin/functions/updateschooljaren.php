<?php

  ini_set('display_errors', 'On');

  require '../checklogin.php';

  if(!$userInfo) {
    echo 'Niet ingelogd.';
    exit;
  }

  if(!defined('LAZER_DATA_PATH')) {
    define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
  }

  use Lazer\Classes\Database as Lazer;

  try{
      \Lazer\Classes\Helpers\Validate::table('schooljaren')->exists();
  } catch(\Lazer\Classes\LazerException $e){
      Lazer::create('schooljaren', array(
        'id' => 'integer',
        'admin' => 'string',
        'frontend' => 'string'
      ));
  }

  $row = Lazer::table('schooljaren');

  $row = $row->find(1);
  $row->admin = $_POST['schooljaren_admin'];
  $row->frontend = $_POST['schooljaren_frontend'];

  $row->save();
  header('Location: ' . $_SERVER['HTTP_REFERER']);



?>
