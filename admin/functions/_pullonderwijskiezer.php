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


  $urlBasis = 'https://www.onderwijskiezer.be/lop_ok/ok_basis.php';
  $urlSecundair = 'https://www.onderwijskiezer.be/lop_ok/ok_secundair.php';

  $type = !empty($_GET['type']) ? $_GET['type'] : false;
  $page = !empty($_GET['page']) ? $_GET['page'] : 1;
  $chunksize = 500;

  if(!defined('LAZER_DATA_PATH')) {
    define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
  }

  use Lazer\Classes\Database as Lazer;

  if($type && $type == 'lager' || $type == 'secundair') {

    $localUrl = dirname(__DIR__, 1).'/apidata/'.'onderwijskiezer'.$type.'.txt';

    if(file_exists($localUrl)) {
      $data = file_get_contents($localUrl);
      echo 'Lokale data gevonden en geladen.';
    } else {
      $url = $type === 'secundair' ? $urlSecundair : $urlBasis;
      $data = file_get_contents($url);
      $data = substr($data,18,-2);
      $localfile = fopen($localUrl,"wb");
      fwrite($localfile,$data);
      fclose($localfile);
      echo 'Externe data geladen en opgeslaan.';
    }

    $richtingen = json_decode($data,true);
    $keys = array_keys($richtingen[0]);

    $fields['id'] = 'integer';
    $fields['last_sync'] = 'string';

    foreach($keys as $key) {
      $fields[$key] = 'string';
    }

    try{
        \Lazer\Classes\Helpers\Validate::table('onderwijskiezer'.$type)->exists();
    } catch(\Lazer\Classes\LazerException $e){
        Lazer::create('onderwijskiezer'.$type, $fields);
    }

    $row = Lazer::table('onderwijskiezer'.$type);

    $i = 0;

    foreach($richtingen as $richting) {

      $row->set(array_change_key_case($richting,CASE_LOWER));
      $row->save();
      echo 'Saved: '.$row->lastId();

    }

  } else {
    echo 'Geen type onderwijs aangegeven.';
  }
