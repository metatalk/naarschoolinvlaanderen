<?php
require '../checklogin.php';

define('LAZER_DATA_PATH', '/var/database/');

use Lazer\Classes\Database as Lazer;

$logs = Lazer::table('log');

var_dump($logs->findAll());
