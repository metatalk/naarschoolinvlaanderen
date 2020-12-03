<?php

require 'checklogin.php';

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}


define('LAZER_DATA_PATH', realpath(__DIR__).'/databases/');

use Lazer\Classes\Database as Lazer;

try{
    \Lazer\Classes\Helpers\Validate::table('entries2018')->exists();
} catch(\Lazer\Classes\LazerException $e){
    echo 'database 2018 bestaat niet';
    Lazer::create('entries2018', array(
      'id' => 'integer',
      'sheetsuKey' => 'integer',
      'School' => 'integer',
      'Plaatsen' => 'integer',
      'PlaatsenBezet' => 'integer',
      'PlaatsenBezetInd' => 'integer',
      'PercentageInd' => 'integer',
      'Volzet' => 'string'
    ));
}
try{
    \Lazer\Classes\Helpers\Validate::table('entries2019')->exists();
} catch(\Lazer\Classes\LazerException $e){
    echo 'database 2019 bestaat niet';
    Lazer::create('entries2019', array(
      'id' => 'integer',
      'sheetsuKey' => 'integer',
      'School' => 'integer',
      'Plaatsen' => 'integer',
      'PlaatsenBezet' => 'integer',
      'PlaatsenBezetInd' => 'integer',
      'PercentageInd' => 'integer',
      'Volzet' => 'string'
    ));
}

if(empty($_GET['school'])) {
  echo 'Geen school.';
  exit;
}

if(empty($_GET['schooljaar'])) {
  echo 'Geen schooljaar.';
  exit;
}

$table = "entries2018";

if($_GET['schooljaar'] == 2019) {
  $table = 'entries2019';
}

$row = Lazer::table($table);
$school = (int)$_GET['school'];
$entries = $row->where('school', '=', $school)->findAll();
$output = array();
if($entries->count() > 0) {
  foreach($entries as $entry) {
    $output[] = $entry;
  }
}
echo json_encode($output);
