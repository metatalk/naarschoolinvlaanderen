<?php

ini_set('display_errors', 'On');

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

try{
    \Lazer\Classes\Helpers\Validate::table(definetable())->exists();
} catch(\Lazer\Classes\LazerException $e){
    if($type == 'lager') {
      Lazer::create(definetable(), $opleidingLager);
    } elseif($type == 'secundair') {
      Lazer::create(definetable(), $opleidingSecundair);
    }
}

function loadopleidingen($vestiging=false,$filter=false) {
  if(!empty($vestiging)) {
    try{
      $opleidingen = Lazer::table(definetable())->where('vestiging_id','=',$vestiging)->findAll();
      return $opleidingen;
    } catch(\Lazer\Classes\LazerException $e){
      return false;
    }
  } else {
    $opleidingen = Lazer::table(definetable())->findAll();
  }
  return $opleidingen;
}

function loadopleidingenAjax($vestiging=false,$filter=false) {
  $opleidingen = loadopleidingen($vestiging,$filter);
  $data = $opleidingen->asArray();
  foreach($data as $key => $opleiding) {
    $data[$key]['sortgraad'] = calculateSOLeerjaar($opleiding);
  }
  return json_encode($data);
}

function calculateSOLeerjaar($opleiding) {
  if(empty($opleiding['leerjaar']) || empty($opleiding['graad'])) {
    return 'zzz'.$opleiding['leerjaar'] . ' ' . $opleiding['graad'];
  } else {

    switch($opleiding['graad']) {
      case 'Eerste graad':
        return $opleiding['leerjaar'] == '1ste leerjaar' ? '1ste middelbaar' : '2de middelbaar';
      break;
      case 'Tweede graad':
        return $opleiding['leerjaar'] == '1ste leerjaar' ? '3de middelbaar' : '4de middelbaar';
      break;
      case 'Derde graad':
        return $opleiding['leerjaar'] == '1ste leerjaar' ? '5de middelbaar' : '6de middelbaar';
      break;
    }
  }
}

function loadschema() {
  return Lazer::table(definetable())->schema();
}
