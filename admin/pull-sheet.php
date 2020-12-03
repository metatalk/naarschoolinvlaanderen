<?php
  require 'checklogin.php';
  if(!$userInfo) {
    echo 'Niet ingelogd.';
    exit;
  }
  require_once __DIR__ . '/google-api/src/Google/autoload.php';

  $client = new Google_Client();
  $client->setApplicationName('naarschoolin');
  $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
  $client->setAccessType('offline');
  $client->setAuthConfig('/var/www/naarschoolin-secure/servicekey.json');
  $service = new Google_Service_Sheets($client);
  $spreadsheetId = "1Q4-mWhOtGiwfhCuEU9Exb9c97M9Thhmn-NTr8kowKwc";

  $onderwijstype = $_GET['Onderwijstype'] ? $_GET['Onderwijstype'] : 'Lager';

  if($onderwijstype != 'Lager' && $onderwijstype != 'Secundair') {
    die('Geen correct onderwijstype');
  }

  $range = $onderwijstype;
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $values = $response->getValues();

  $json = [];

  $columns = $values[0];
  $values[0] = '';

  if (empty($values)) {
     print 'No data found.\n';
  } else {
     $row = 0;
     foreach ($values as $cell) {
       for ($i = 0; $i < sizeof($cell); $i++) {
          $column = $columns[$i];
          $json[$row][$column] = $cell[$i];
       }
       $row++;
     }
  }

  $removed = array_shift($json);

  $json = json_encode($json);

  $file = '/var/www/naarschoolin/application/apidata/data/'.$onderwijstype.'.json';

  if(!file_exists($file)) {
    $handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
    $write = fwrite($handle,$json) or die('Fout');
    echo $json;
    fclose($file);
  }
