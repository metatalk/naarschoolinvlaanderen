<?php

ini_set('display_errors', 'On');

require '../checklogin.php';
require '_schemas.php';

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}

if(!defined('LAZER_DATA_PATH')) {
  define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
}

use Lazer\Classes\Database as Lazer;

$typeonderwijs = array('lager','secundair');

if(empty($_POST['type']) || !in_array($_POST['type'],$typeonderwijs)) {
  echo 'Geen geldig type onderwijs.';
  exit;
} else {
  $type = $_POST['type'];
  $table = 'opleidingen'.$type;
}

if(empty($_POST['vestiging_id'])) {
  echo 'Geen geldige vestiging.';
  exit;
}

try{
    \Lazer\Classes\Helpers\Validate::table($table)->exists();
} catch(\Lazer\Classes\LazerException $e){
    if($type == 'lager') {
      Lazer::create($table, $opleidingLager);
    } elseif($type == 'secundair') {
      Lazer::create($table, $opleidingSecundair);
    }
}

$row = Lazer::table($table);

session_start();

if(!empty($_POST['new'])) {
  foreach($_POST['new'] as $save) {
    if(!empty($save['opleiding'])) {
      unset($save['id']);
      $save['vestiging_id'] = (int) $save['vestiging_id'];
      $row->set($save);
      $row->save();
      $_SESSION["saved"] = "yes";
    }
  }
}

if(!empty($_POST['existing'])) {
  foreach($_POST['existing'] as $save) {
    $row = $row->find((int) $save['id']);
    if(!empty($row) && !empty($save['opleiding'])) {
      unset($save['id']);
      $save['vestiging_id'] = (int) $save['vestiging_id'];
      $row->set($save);
      $row->save();
      $_SESSION["saved"] = "yes";
    }
  }
}

//header('Location: ' . $_SERVER['HTTP_REFERER']);
