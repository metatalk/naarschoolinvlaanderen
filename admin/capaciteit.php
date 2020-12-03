<?php
  require 'checklogin.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <title>Naar School in Vlaanderen</title>

  </head>
  <body>
    <?php require 'userbar.php'; ?>
    <?php
      include 'functions/loadvestigingen.php';
      $vestigingen = loadvestigingen();
    ?>

    <div class="container">
      <h1 class="mb-5">Kies de instelling waarvan je de cijfers wil aanpassen.</h1>
      <table class="table">
        <tbody>
        <?php $i=0; foreach($vestigingen as $vestiging): ?>

            <?php if(!$isSuperAdmin && !in_array($vestiging->id,$hasAccessTo)): ?>

            <?php else: $i++; ?>
              <tr style="<?php if(!$isSuperAdmin && !in_array($vestiging->id,$hasAccessTo)) { echo 'opacity:0.2;'; } ?>">
                <td>
                  <h5><?php echo $vestiging->naam; ?></h5>
                  <small class="d-block mt-2">
                    <?php echo $vestiging->adres.'<br> '. $vestiging->postcode. ' ' . $vestiging->gemeente; ?>
                  </small>
                </td>
                <td class="text-right align-middle">
                  <a href="aanpassen.php?type=<?php echo $vestiging->type; ?>&vestiging=<?php echo $vestiging->id; ?>" class="btn btn-primary btn-small <?php if(!$isSuperAdmin && !in_array($vestiging->id,$hasAccessTo)) { echo 'disabled'; } ?>"><i class="fas fa-table"></i> Cijfers aanpassen</a>
                  <a href="createopleiding.php?type=<?php echo $vestiging->type; ?>&vestiging=<?php echo $vestiging->id; ?>" class="btn btn-primary btn-small <?php if(!$isSuperAdmin && !in_array($vestiging->id,$hasAccessTo)) { echo 'disabled'; } ?>"><i class="fas fa-graduation-cap"></i> Opleidingen aanpassen</a>
                </td>
              </tr>
            <?php endif; ?>

        <?php endforeach; ?>
        </tbody>
      </table>
      <?php if($i == 0): ?>
        <div class="alert" role="alert">
          Er zijn geen vestigingen aan je account gekoppeld. Neem contact op met de beheerder.
        </div>
      <?php endif; ?>
    </div>

    <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.min.js"></script>
    <script type="text/javascript" src="jlinq.js"></script>
    <script type="text/javascript" src="export.js"></script>

  </body>
</html>
