<?php

  require '../checklogin.php';

  if(!$userInfo) {
    echo 'Niet ingelogd.';
    exit;
  }

  if(!defined('LAZER_DATA_PATH')) {
    define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
  }

  use Lazer\Classes\Database as Lazer;

  if(empty($_GET['id'])) {
    echo 'Geen vestiginsnummer ingegeven.';
    exit;
  }

  $data = Lazer::table('onderwijskiezeralle');

  $results = $data->where('nummer_instelling','=',$_GET['id'])->orderBy('korte_naam')->orderBy('natuurlijk_leerjr')->findAll()->asArray();

  echo json_encode($results);
