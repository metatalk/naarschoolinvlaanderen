<?php

ini_set('display_errors', 'On');

require '../checklogin.php';

if(!$userInfo) {
  echo 'Niet ingelogd.';
  exit;
}

if(!defined('LAZER_DATA_PATH')) {
  define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
}

use Lazer\Classes\Database as Lazer;

$table = 'vestigingen';

if(empty($_POST['naam']) || empty($_POST['type'])) {
  echo 'Naam of type onderwijs niet aangeduid.';
  exit;
}

try{
    \Lazer\Classes\Helpers\Validate::table($table)->exists();
} catch(\Lazer\Classes\LazerException $e){
    Lazer::create($table, array(
      'id' => 'integer',
      'naam' => 'string',
      'adres' => 'string',
      'postcode' => 'string',
      'gemeente' => 'string',
      'hoofdgemeente' => 'string',
      'website' => 'string',
      'updated' => 'string',
      'type' => 'string',
      'vestigings_nummer' => 'string'
    ));
}

$row = Lazer::table($table);

if(!empty($_POST['id'])) {

  $findEntry = $row->where('id', '=', $_POST['id'])->find();

  if($findEntry->count() == 1) {
      $row = $findEntry;
  }

}

$row->set(array(
  'naam' => $_POST['naam'],
  'adres' => $_POST['adres'],
  'postcode' => $_POST['postcode'],
  'gemeente' => $_POST['gemeente'],
  'hoofdgemeente' => $_POST['hoofdgemeente'],
  'website' => $_POST['website'],
  'updated' => date("Y/m/d H:i:s"),
  'type' => $_POST['type'],
  'vestigings_nummer' => $_POST['vestigings_nummer']
));

$row->save();

// toevoegen aan users
if(empty($_POST['id']) && $canEditVestigingen) {
  $id = $row->lastId();
  if($hasAccessTo === false) {
    $hasAccessTo = array();
  }
  array_push($hasAccessTo,$id);
  $saveUserdata['user_metadata']['school'] = $hasAccessTo;
  try {
    $mgmt_api->users->update($userData['user_id'],$saveUserdata);
  } catch (Exception $e) {
      $error = '<div class="alert alert-danger" role="alert">';
      $error = 'Caught exception: '.  $e->getMessage();
      $error = '</div>';
  }
}

if(empty($error)) {
  header('Location: ' . $_SERVER['HTTP_REFERER'].'?id='.$row->lastId().'&new=true');
} else {
  echo $error;
}
