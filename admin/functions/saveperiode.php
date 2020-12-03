<?php

  ini_set('display_errors', 'On');

  require '../checklogin.php';

  if(!defined('LAZER_DATA_PATH')) {
    define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
  }

  use Lazer\Classes\Database as Lazer;

  try{
      \Lazer\Classes\Helpers\Validate::table('periode')->exists();
  } catch(\Lazer\Classes\LazerException $e){
      Lazer::create('periode', array(
        'id' => 'integer',
        'periode' => 'string',
        'datum' => 'string'
      ));
  }

  $row = Lazer::table('periode');

  $row->periode = $_POST['periode'];
  $row->datum = date("Y/m/d H:i:s");

  $row->save();
  header('Location: ' . $_SERVER['HTTP_REFERER']);



?>
