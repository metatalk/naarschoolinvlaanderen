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
    <link rel="stylesheet" href="css/main.css">
    <title>Naar School in Limburg</title>

  </head>
  <body>
    <?php require 'userbar.php'; ?>
    <div class="container">
      <?php

        if(!empty($_GET['id'])) {
          require 'functions/loadvestigingen.php';
          $vestiging = loadvestigingen($_GET['id']);
          if(empty($vestiging)) {
            $error = 'Geen vestiging gevonden met id '. $_GET['id'];
          }
        }

        if(empty($_GET['id']) && !$isSuperAdmin && !$canEditVestigingen) {
          $error = 'Je hebt geen rechten om een nieuwe vestiging aan te maken.';
        }

      ?>

        <?php if(!empty($error)) : ?>

          <div class="p-5">
            <div class="alert alert-danger" role="alert">
              <?= $error; ?>
            </div>
          </div>

        <?php else: ?>

        <?php if(!empty($_GET['new'])): ?>
          <div class="p-5">
            <div class="alert alert-success" role="alert">
              Je vestiging is succesvol opgeslaan! Je kan nu <a class="" href="/admin/createopleiding.php?type=<?= $vestiging->type; ?>&vestiging=<?= $vestiging->id; ?>">opleidingen toevoegen en aanpassen</a>.
            </div>
          </div>
        <?php endif; ?>

        <h1 class="mb-1">
          <?= !empty($vestiging) ? 'Pas gegevens van '.$vestiging->naam.' aan' : 'Voeg een nieuwe vestiging toe'; ?>
        </h1>

        <?php if(!empty($vestiging)): ?>
          <p><a class="" href="/admin/createopleiding.php?type=<?= $vestiging->type; ?>&vestiging=<?= $vestiging->id; ?>">Opleidingen toevoegen en aanpassen</a></p>
        <?php endif; ?>

        <form class="form mt-4" action="functions/savevestiging.php" method="post">
          <?php if(!empty($vestiging)): ?>
            <input type="hidden" name="id" value="<?= $vestiging->id; ?>">
          <?php endif; ?>
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label for="naam">Naam vestiging</label>
                <input required value="<?= !empty($vestiging->naam) ? $vestiging->naam : '' ?>" type="text" name="naam" class="form-control" id="naam" placeholder="Naam">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="vestigings_nummer">Vestigingsnummer</label>
                <input required value="<?= !empty($vestiging->vestigings_nummer) ? $vestiging->vestigings_nummer : '' ?>" type="text" name="vestigings_nummer" class="form-control" id="vestigings_nummer" placeholder="Vestigingsnummer">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="adres">Straat en nr.</label>
            <input value="<?= !empty($vestiging->adres) ? $vestiging->adres : '' ?>" type="text" name="adres" class="form-control" id="adres" placeholder="Straat en nr">
          </div>
          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
                <label for="postcode">Postcode</label>
                <input type="text" value="<?= !empty($vestiging->postcode) ? $vestiging->postcode : '' ?>" name="postcode" class="form-control" id="postcode" placeholder="Postcode">
              </div>
            </div>
            <div class="col-md-7">
              <div class="form-group">
                <label for="gemeente">Gemeente</label>
                <input type="text" value="<?= !empty($vestiging->gemeente) ? $vestiging->gemeente : '' ?>"  name="gemeente" class="form-control" id="gemeente" placeholder="Gemeente">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="hoofdgemeente">Hoofdgemeente</label>
            <input type="text" name="hoofdgemeente" value="<?= !empty($vestiging->hoofdgemeente) ? $vestiging->hoofdgemeente : '' ?>"  class="form-control" id="hoofdgemeente" placeholder="Hoofdgemeente">
          </div>
          <div class="form-group">
            <label for="website">Website</label>
            <input type="text" name="website" value="<?= !empty($vestiging->website) ? $vestiging->website : '' ?>" class="form-control" id="website" placeholder="Website">
          </div>
          <div class="form-group">
            <div class="form-check form-check-inline">
              <input class="form-check-input" <?= !empty($vestiging->type) && $vestiging->type == 'lager' ? 'checked' : '' ?> type="radio" name="type" id="type1" value="lager">
              <label class="form-check-label" for="type1">Kleuter en lager</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" <?= !empty($vestiging->type) && $vestiging->type == 'secundair' ? 'checked' : '' ?>  type="radio" name="type" id="type2" value="secundair">
              <label class="form-check-label" for="type2">Secundair</label>
            </div>
          </div>
          <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-lg btn-primary" name="button">
              <?= !empty($vestiging) ? 'Vestiging aanpassen' : 'Vestiging toevoegen'; ?>
            </button>
            <?php if(!empty($vestiging) && ($canEditVestigingen === true || $isSuperAdmin)): ?>
              <a onclick="if(confirm('Ben je zeker dat je deze vestiging wil verwijderen?') == false) { return false; }" class="btn text-danger" href="/admin/functions/deletevestiging.php?id=<?= $vestiging->id; ?>">Vestiging verwijderen</a>
            <?php endif; ?>
          </div>


        </form>

      <?php endif; ?>
    </div>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.min.js"></script>
    <script type="text/javascript" src="jlinq.js"></script>

    <!-- Templates -->
    <script id="input-row" type="text/x-handlebars-template">



    </script>

  </body>
</html>
