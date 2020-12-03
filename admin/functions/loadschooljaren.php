<?php

  if(!defined('LAZER_DATA_PATH')) {
    define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
  }

  use Lazer\Classes\Database as Lazer;

  function loadschooljaren() {
    $row = Lazer::table('schooljaren');
    $row = $row->find(1);
    return $row;
  }

?>
