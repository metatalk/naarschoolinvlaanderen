<?php

ini_set('display_errors', 'On');

require '../checklogin.php';

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}

$schooljaren = array('2018','2019','2020','2021','2022','2023','2024');

if(empty($_GET['schooljaar']) || !in_array($_GET['schooljaar'],$schooljaren)) {
  echo 'Geen geldig schooljaar.';
  exit;
} else {
  $schooljaar = $_GET['schooljaar'];
  $table = "entries".$_GET['schooljaar'];
}

$typeonderwijs = array('lager','secundair');

if(empty($_GET['type']) || !in_array($_GET['type'],$typeonderwijs)) {
  echo 'Geen geldig type onderwijs.';
  exit;
} else {
  $type = $_GET['type'];
  $table = $table.$type;
}

if(empty($_GET['opleiding_id'])) {
  echo 'Geen key.';
  exit;
} else {
  $id =  $_GET['opleiding_id'];
}
define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');

use Lazer\Classes\Database as Lazer;

try{
    \Lazer\Classes\Helpers\Validate::table($table)->exists();
} catch(\Lazer\Classes\LazerException $e){
    echo 'Database bestaat niet';
    Lazer::create($table, array(
      'id' => 'integer',
      'opleiding_id' => 'integer',
      'plaatsen' => 'integer',
      'plaatsenbezet' => 'integer',
      'plaatsenbezetind' => 'integer',
      'percentageind' => 'double',
      'datum' => 'string',
      'volzet' => 'string',
      'plaatsenanderstalig' => 'integer',
      'plaatsenanderstaligbezet' => 'integer',
      'percentageindtonen' => 'string',
      'hide' => 'string'
    ));
}

$row = Lazer::table($table);

$findEntry = $row->where('opleiding_id', '=', $id)->find();

if($findEntry->count() == 1) {
    $row = $findEntry;
} else {
    $row->opleiding_id = (int) $id;
}

$field = mb_strtolower($_GET['field']);
$value = $_GET['value'];


if(is_numeric($value)) {
  $value = floatval($value);
}

$row->{$field} = $value;
$row->datum = date("Y/m/d H:i:s");

try{
  $row->save();
  echo 'ok';
} catch(\Lazer\Classes\LazerException $e){
  echo $e;
}
