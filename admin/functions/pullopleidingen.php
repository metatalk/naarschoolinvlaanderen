<?php

  require '../checklogin.php';

  if(!$userInfo) {
    echo 'Niet ingelogd.';
    exit;
  }

  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  ini_set('display_startup_errors', 1);
  ini_set('max_execution_time', 6000);
  ini_set('memory_limit', '-1');
  set_time_limit(6000);

  if(!defined('LAZER_DATA_PATH')) {
    define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
  }

  use Lazer\Classes\Database as Lazer;

  $localUrl = dirname(__DIR__, 1).'/apidata/richtingen_google.json';

  if(file_exists($localUrl)) {
    $data = file_get_contents($localUrl);
    echo 'Lokale data gevonden en geladen.';
  } else {
    die('Bestand niet gevonden.');
  }

  $richtingen = json_decode($data,true);
  $keys = array_keys($richtingen[0]);

  $fields['id'] = 'integer';
  $fields['last_sync'] = 'string';

  foreach($keys as $key) {
    $fields[$key] = 'string';
  }

  try{
      \Lazer\Classes\Helpers\Validate::table('onderwijskiezeralle')->exists();
  } catch(\Lazer\Classes\LazerException $e){
      Lazer::create('onderwijskiezeralle', $fields);
  }

  $row = Lazer::table('onderwijskiezeralle');

  $i = 0;

  foreach($richtingen as $richting) {

    $row->set(array_change_key_case($richting,CASE_LOWER));
    //$row->save();
    echo 'Saved: '.$row->lastId();

  }
