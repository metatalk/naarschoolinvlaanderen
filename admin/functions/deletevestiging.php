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

if(empty($_GET['id'])) {
  echo 'Geen geldig id.';
  exit;
}

Lazer::table('vestigingen')->find($_GET['id'])->delete();

header('Location: /admin?removedvestiging=true');
