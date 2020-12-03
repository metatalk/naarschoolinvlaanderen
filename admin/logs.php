<?php
  ini_set('display_errors', 'On');

  require 'checklogin.php';

  define('LAZER_DATA_PATH', realpath(__DIR__).'/databases/');

  use Lazer\Classes\Database as Lazer;

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <link rel="stylesheet" href="css/main.css">
    <title>Naar School in Vlaanderen</title>

  </head>
  <body>
    <?php require 'userbar.php'; ?>
    <div class="container">

      <?php
        include ('functions/loadlog.php');
        $logs = loadLogs(false,'backuplog');

      ?>
      <div class="p-0">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Gebruiker</th>
              <th scope="col">School</th>
              <th scope="col">Aangepaste data</th>
              <th scope="col">Datum</th>
            </tr>
          </thead>
          <tbody id="logs">
            <?php foreach($logs as $log): ?>
              <?php if($isSuperAdmin || $log->userid === $userData['user_id'] || in_array($log->schoolid,$hasAccessTo)): ?>
              <tr data-filtertext="<?= strtolower($log->user . ' ' . $log->school . ' ' .$log->data) ?>" <?php if($log->data == 'volzet'): echo 'class="log-volzetVerklaring"'; endif;?>>
                <td><?php echo $log->user; ?></td>
                <td><?php echo $log->school; ?></td>
                <td><?php echo $log->data; ?> naar <?php echo $log->waarde; ?></td>
                <td><?php echo $log->datum; ?></td>
              </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>

  </body>
</html>
