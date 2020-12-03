<?php

ini_set('display_errors', 'On');

require '../checklogin.php';

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}

$typeonderwijs = array('lager','secundair');
if(empty($_GET['type']) || !in_array($_GET['type'],$typeonderwijs)) {
  echo 'Geen geldig type onderwijs.';
  exit;
}

$type = $_GET['type'];

function definetable() {
  $type = $_GET['type'];
  $table = 'opleidingen'.$type;
  return $table;
}

if(!defined('LAZER_DATA_PATH')) {
  define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
}

use Lazer\Classes\Database as Lazer;

if(empty($_GET['id'])) {
  echo 'Geen geldig id.';
  exit;
}

Lazer::table(definetable())->find($_GET['id'])->delete();

header('Location: ' . $_SERVER['HTTP_REFERER']);
