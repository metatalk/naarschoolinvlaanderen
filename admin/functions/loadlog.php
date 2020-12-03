<?php

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}

if(!defined('LAZER_DATA_PATH')) {
  define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
}

use Lazer\Classes\Database as Lazer;

$offset = !empty($_GET['offset']) ? $_GET['offset'] : 0;

function loadLogs($limit=false,$table='log') {
  $logs = Lazer::table($table)->orderBy('id','DESC');
  if($limit !== false) {
    $logs->limit($limit);
  }
  return $logs->findAll();
}
