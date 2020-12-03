<?php

require 'checklogin.php';

if(!empty($_POST)) {
  unset($_POST['search']);
  try {
      if(!empty($_GET['userid'])) {
        $newUser = $mgmt_api->users->update($_GET['userid'],$_POST);
      } else {
        $newUser = $mgmt_api->users->create($_POST);
      }
      echo '<div class="alert alert-success" role="alert">Gebruiker met succes opgeslaan.</div>';
  } catch (Exception $e) {
      echo '<div class="alert alert-danger" role="alert">';
      echo 'Caught exception: ',  $e->getMessage(), "\n";
      echo '</div>';
  }
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
    <title>Naar School in Vlaanderen</title>

  </head>
  <body>
    <?php
      session_start();
      require 'userbar.php';
      if(!empty($_GET['userid'])) {
        $currentUser = $mgmt_api->users->get($_GET['userid']);
      }
    ?>

    <?php if(!empty($_SESSION['error'])):  ?>
      <div class="pt-2 pb-5">
        <div class="alert alert-danger" role="alert">
          <?= $_SESSION['error']; ?>
        </div>
      </div>
    <?php $_SESSION['error'] = ""; endif; ?>

    <?php if(!empty($_SESSION['success'])): ?>
      <div class="pt-2 pb-5">
        <div class="alert alert-success" role="alert">
          De gebruiker is succesvol opgeslaan.
        </div>
      </div>
    <?php $_SESSION['success'] = ""; endif; ?>

    <div class="container">
      <h1 class="mb-5">
        <?= empty($_GET['userid']) ? 'Nieuwe gebruiker toevoegen' : 'Gebruiker '.$currentUser['given_name']. ' aanpassen'; ?>
      </h1>

      <form method="post" action="functions/saveuser.php">

        <?php if(!empty($_GET['userid'])): ?>
          <input type="hidden" name="userid" value="<?= $_GET['userid'] ?>">
        <?php endif; ?>

        <?php if($canEditUsers): ?>
          <input type="hidden" name="user_metadata[belongsTo]" value="<?= $userData['user_id']; ?>">
        <?php endif; ?>

        <div class="row">

          <div class="col-md-5">

              <input type="hidden" name="connection" value="Naarschoolin">
              <div class="form-group">
                <label for="voornaam">Voornaam</label>
                <input type="text" name="given_name" value="<?= $currentUser['given_name'] ? $currentUser['given_name'] : '' ?>" class="form-control" id="voornaam" placeholder="Voornaam">
              </div>
              <div class="form-group">
                <label for="naam">Naam</label>
                <input type="text" name="family_name" value="<?= $currentUser['family_name'] ? $currentUser['family_name'] : '' ?>" class="form-control" id="naam" placeholder="Naam">
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">E-mail adres</label>
                <input type="email" name="email" value="<?= $currentUser['email'] ? $currentUser['email'] : '' ?>" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="E-mail adres">
              </div>

              <?php if(empty($_GET['userid'])): ?>
                <div class="form-group">
                  <label for="exampleInputPassword1">Wachtwoord</label>
                  <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Wachtwoord">
                  <p class="alert alert-warning mt-3">
                    <small>Minimum 6 tekens en moet 1 cijfer en 1 speciaal teken bevatten!</small>
                  </p>
                </div>
              <?php endif; ?>

              <button type="submit" class="btn btn-primary">
                <?= empty($_GET['userid']) ? 'Gebruiker opslaan' : 'Gebruiker aanpassen'; ?>
              </button>

          </div>

          <div class="col-md-7">

            <?php if($isSuperAdmin || $canEditUsers || $canEditVestigingen): ?>
              <div class="log card mb-3 p-0">
                <div class="bg-dark text-white pt-3 pb-3 px-4 justify-content-center align-items-center">
                  <h6>Selecteer rechten voor de gebruiker</h6>
                </div>

                <div class="p-3">
                  <?php if($isSuperAdmin || $canEditVestigingen): ?>
                    <div class="form-group form-check">
                      <input type="checkbox" <?= $currentUser['user_metadata']['editvestigingen'] ? 'checked' : '' ?> class="form-check-input" name="user_metadata[editvestigingen]" value="1" id="editvestigingen">
                      <label class="form-check-label" for="editvestigingen">Gebruiker mag nieuwe vestigingen aanmaken</label>
                    </div>
                  <?php endif; ?>
                  <?php if($isSuperAdmin || $canEditUsers): ?>
                    <div class="form-group form-check">
                      <input type="checkbox" <?= $currentUser['user_metadata']['editusers'] ? 'checked' : '' ?> class="form-check-input" name="user_metadata[editusers]" value="1" id="editusers">
                      <label class="form-check-label" for="editusers">Gebruiker mag nieuwe gebruikers aanmaken</label>
                    </div>
                  <?php endif; ?>
                </div>

              </div>
            <?php endif; ?>

            <div class="log card p-0">


              <div class="row bg-dark text-white pt-3 pb-3 px-4 justify-content-center align-items-center">
                <div class="col-md-7">
                  <h6>Selecteer de instellingen waartoe deze gebruiker toegang heeft.</h6>
                </div>
                <div class="col-md-5">
                  <input type="text" name="search" data-filter="#vestigingen" class="form-control" placeholder="Zoek op school of gemeente">
                </div>
              </div>

              <div style="height:500px;overflow:scroll;" id="vestigingen">

                <?php
                  include 'functions/loadvestigingen.php';
                  $hasAccessTo = $isSuperAdmin ? true : $hasAccessTo;
                  $vestigingen = loadvestigingen(false,array('id'=>$hasAccessTo));
                ?>

                <?php $i=0; foreach($vestigingen as $vestiging): ?>

                    <div class="form-group border-bottom m-0 form-check p-3 pl-5" data-filtertext="<?php echo strtolower($vestiging->naam . ' ' . $vestiging->gemeente . ' ' .$vestiging->hoofdgemeente); ?>">
                      <input type="checkbox" <?= !empty($currentUser['user_metadata']['school']) && in_array($vestiging->id,$currentUser['user_metadata']['school']) ? 'checked' : '' ?> class="form-check-input" name="user_metadata[school][]" value="<?php echo $vestiging->id; ?>" id="school-<?php echo $vestiging->id; ?>">
                      <label class="form-check-label" for="school-<?php echo $vestiging->id; ?>"><?php echo $vestiging->naam; ?>
                        <span class="badge <?php echo $vestiging->type == 'lager' ? 'badge-info' : 'badge-warning'; ?> mt-n4"><?= $vestiging->type == 'lager' ? "Kleuter en/of lager" : "Secundair"; ?></span>
                        <br> <small><?php echo $vestiging->adres. ' ' . $vestiging->gemeente; ?></small>
                      </label>
                    </div>

                <?php endforeach; ?>

              </div>

            </div>

          </div>

        </div>

      </form>

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
    <script type="text/javascript" src="js/filter.js"></script>
  </body>
</html>
