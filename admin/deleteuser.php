<?php

require 'checklogin.php';

if(!$isSuperAdmin) {
  //die('Geen toegang.');
}

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <title>Naar School in Limburg</title>

  </head>
  <body>
    <?php require 'userbar.php'; ?>

    <div class="container">
      <?php
        if(!empty($_GET['userid'])) {
          try {
              $mgmt_api->users->delete($_GET['userid']);
              header('Location: ' . $_SERVER['HTTP_REFERER']);
          } catch (Exception $e) {
              echo '<div class="alert alert-danger" role="alert">';
              echo 'Caught exception: ',  $e->getMessage(), "\n";
              echo '</div>';
          }
        }
      ?>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#volzetTonen').on('change',function() {
          if($(this).prop("checked")) {
            $('.log tbody tr:not(.log-volzetVerklaring)').hide();
          } else {
            $('.log tbody tr').show();
          }
        });
      });
    </script>
  </body>
</html>
