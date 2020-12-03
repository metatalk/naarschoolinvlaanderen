<?php

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}

if(!defined('LAZER_DATA_PATH')) {
  define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
}

use Lazer\Classes\Database as Lazer;

function loadvestigingen($vestiging=false,$filter=false) {
  $table = 'vestigingen';
  $vestigingen = Lazer::table($table);
  $result = array();
  if(!empty($vestiging)) {
    try{
      $result = $vestigingen->find($vestiging);
    } catch(\Lazer\Classes\LazerException $e){
      return false;
    }
  } else if(is_array($filter) && !empty($filter['id']) && is_array($filter['id'])) {
    $result = $vestigingen->orderBy('id', 'DESC')->where('id', 'IN', $filter['id'])->findAll();
  } else if($filter['id'] === true) {
    $result = $vestigingen->orderBy('id', 'DESC')->findAll();
  }

  return $result;
}
