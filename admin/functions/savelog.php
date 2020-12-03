<?php

require '../checklogin.php';

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}

if(empty($_GET['logdata'])) {
  echo 'Foute data voor te loggen';
  exit;
}

if(!defined('LAZER_DATA_PATH')) {
  define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
}

use Lazer\Classes\Database as Lazer;

try{
    \Lazer\Classes\Helpers\Validate::table('log')->exists();
} catch(\Lazer\Classes\LazerException $e){
    echo 'Database bestaat niet';
    Lazer::create('log', array(
      'id' => 'integer',
      'user' => 'string',
      'userid' => 'string',
      'data' => 'string',
      'waarde' => 'string',
      'school' => 'string',
      'schoolid' => 'integer',
      'datum' => 'string'
    ));
}

$log = Lazer::table('log');

$logdata = json_decode($_GET['logdata'],true);

if(is_array($logdata) && !empty($logdata['data']) && !empty($logdata['waarde'])) {

  $log->user = $userInfo['name'];
  $log->userid = $userData['user_id'];
  $log->data = $logdata['data'];
  $log->waarde = $logdata['waarde'];
  $log->school = $logdata['school'];
  $log->schoolid = (int) $logdata['schoolid'];
  $log->datum = date("Y/m/d H:i:s");

  if($log->data == "plaatsen") {
    $log->data = "capaciteit";
  }

  if($log->data == "volzet" && empty($log->waarde)) {
    $log->waarde = 'nee';
  }

  echo $log->save();
}
