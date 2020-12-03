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

      <?php if(!empty($_GET['removedvestiging']) && $_GET['removedvestiging'] == 'true'): ?>
        <div class="alert alert-success mb-5">
          Je vestiging is succesvol verwijderd.
        </div>
      <?php endif; ?>

      <h1 class="mb-4">Naar school in Vlaanderen</h1>

      <div class="row">
        <div class="col-md-3">

          <div class="pr-3">
            <?php if($isSuperAdmin || $canEditVestigingen): ?>
            <a href="createvestiging.php" class="d-block btn btn-primary p-2  mb-2">
              <i class="fas fa-building"></i> Vestiging toevoegen
            </a>
            <?php endif; ?>

            <?php if($isSuperAdmin || $canEditUsers): ?>
            <a href="users.php" class="d-block btn btn-primary p-2">
              <i class="fas fa-user"></i> Gebruikers aanpassen
            </a>
            <?php endif; ?>
            <?php if($isSuperAdmin): ?>
              <div class="bg-light p-3 mt-4">
                <p>Welke schooljaren zijn beschikbaar?</p>
                <p>
                  <small>Vul de leerjaren met een komma. bv. 2020,2021,2022</small>
                </p>

                <?php
                  include ('functions/loadschooljaren.php');
                  $schooljaren = loadschooljaren();
                ?>

                <form method="post" action="functions/updateschooljaren.php">
                  <div class="form-group">
                    <label for="schooljaren_admin"><strong>In de admin</strong></label>
                    <input type="text" name="schooljaren_admin" value="<?= $schooljaren->admin; ?>" class="form-control" id="schooljaren_admin">
                  </div>
                  <div class="form-group">
                    <label for="schooljaren_frontend"><strong>In de frontend</strong></label>
                    <input type="text" name="schooljaren_frontend" value="<?= $schooljaren->frontend; ?>" class="form-control" id="schooljaren_frontend">
                  </div>
                  <button type="submit" class="btn btn-primary btn-md">Update</button>
                </form>

              </div>
            <?php endif; ?>
            <?php if(!$isSuperAdmin && !$canEditVestigingen && !$canEditUsers): ?>
              <div class="bg-light p-3" style="font-size:0.8rem">
                <p>
                  Dag <?= $userData['given_name']; ?>, <br><br>
                  In het overzicht rechts vind je alle vestigingen terug waarvoor je de capaciteiten en opleidingen kan beheren.
                </p>
                <!--<p>Heb je vragen of opmerkingen? Dan kan je terecht bij:</p>
                <?php
                  if($userbelongsTo) {
                    $belongsTo = $mgmt_api->users->get( $userbelongsTo );
                    echo '<p><strong>'.$belongsTo['given_name']. ' ' .$belongsTo['family_name'].'</strong> <br> ';
                    echo '<a href="mailto:'.$belongsTo['email'].'">'.$belongsTo['email'].'</a></p>';
                  } else {
                    echo '<p><strong>Wim Verkammen</strong> <br> ';
                    echo '<a href="mailto:wim.verkammen@ond.vlaanderen.be">wim.verkammen@ond.vlaanderen.be</a></p>';
                  }
                  ?>-->
              </div>
            <?php endif; ?>
          </div>



          <?php if($isSuperAdmin): ?>
            <!--<div class="card p-3 pt-4 mt-4">
              <h6>Huidige inschrijfperiode</h6>
              <form class="" action="functions/saveperiode.php" method="post">
                <input type="text" required class="form-control" name="periode" value="<?php echo $periode->periode; ?>">
                <input type="submit" class="btn btn-primary mt-2" name="" value="Aanpassen">
              </form>
            </div>-->
          <?php endif; ?>
        </div>
        <div class="col-md-9">
          <?php
            include 'functions/loadvestigingen.php';
            $hasAccessTo = $isSuperAdmin ? true : $hasAccessTo;
            $vestigingen = loadvestigingen(false,array('id'=>$hasAccessTo));
          ?>
          <div class="log card mb-5 p-0">

            <div class="row bg-dark text-white pt-3 pb-3 px-4 justify-content-center align-items-center">
              <div class="col-md-7">
                <h5 class="mb-0">Vestigingen</h5>
              </div>

              <div class="col-md-5">
                <?php if(!empty($vestigingen)): ?>
                  <input type="text" name="search" data-filter="#vestigingen" class="form-control" placeholder="Zoek op school of gemeente">
                <?php endif; ?>
              </div>

            </div>

            <div  style="height:500px;overflow:scroll;">
            <?php if(!empty($vestigingen)): ?>
              <table class="table table-striped">
                <tbody id="vestigingen">
                <?php $i=0; foreach($vestigingen as $vestiging): ?>
                    <tr data-filtertext="<?php echo strtolower($vestiging->naam . ' ' . $vestiging->gemeente . ' ' .$vestiging->hoofdgemeente); ?>">
                      <td class="pl-4">
                        <h6>
                          <strong><?php echo $vestiging->naam; ?></strong>
                          <span class="badge <?php echo $vestiging->type == 'lager' ? 'badge-info' : 'badge-warning'; ?> mt-n4">
                            <?= $vestiging->type == 'lager' ? "Kleuter en/of lager" : "Secundair"; ?>
                          </span>
                        </h6>
                        <small class="d-block mt-2">
                          <?php echo $vestiging->adres.'<br> '. $vestiging->postcode. ' ' . $vestiging->gemeente; ?>
                          <?php if(!empty($vestiging->hoofdgemeente)): ?>(<?= $vestiging->hoofdgemeente ?>)<?php endif; ?>
                        </small>
                        <div class="my-3 font-weight-bold">
                          <div class="pr-3 d-inline-block"><a href="aanpassen.php?type=<?php echo $vestiging->type; ?>&vestiging=<?php echo $vestiging->id; ?>"><i class="fas fa-table"></i> Cijfers aanpassen</a></div>
                          <div class="pr-3 d-inline-block"><a href="createopleiding.php?type=<?php echo $vestiging->type; ?>&vestiging=<?php echo $vestiging->id; ?>"><i class="fas fa-graduation-cap"></i> Opleidingen aanpassen</a></div>
                          <div class="d-inline-block"><a href="createvestiging.php?id=<?php echo $vestiging->id; ?>" ><i class="fas fa-building"></i> Gegevens aanpassen</a></div>
                        </div>
                      </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div class="p-5 text-center">
                Je hebt nog geen vestigingen toegevoegd. <a href="/admin/createvestiging.php">Voeg je eerste vestiging toe</a>
              </div>
            <?php endif; ?>
            </div>
          </div>

          <div class="log card p-0" id="log">
            <div class="bg-dark text-white pt-3 pb-3 px-4 justify-content-center align-items-center">

                <h5 class="mb-0">Recente acties</h5>
                <p>
                  <small>Je kan de recente acties zien van alle vestigingen waar je toegang toe hebt.</small>
                </p>

                <?php if(!empty($vestigingen)): ?>
                  <div class="mb-2">
                    <input type="text" name="search" data-filter="#logs" class="form-control" placeholder="Zoek op school of gemeente">
                  </div>
                <?php endif; ?>

                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="volzetTonen">
                  <label class="form-check-label" for="volzetTonen">
                    Enkel acties volzetverklaringen tonen
                  </label>
                </div>

            </div>
            <?php
              include ('functions/loadlog.php');
              $logs = loadLogs(350);
              if(count($logs) > 0):
            ?>
            <div class="log p-0">
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
            <?php else: ?>
              <p class="p-3 text-center">Nog geen activiteit geregistreerd.</p>
            <?php endif; ?>
            <div class="p-2 bg-light">
              <?php if($isSuperAdmin): ?>
                <small>In deze log worden de recentste 350 acties getoond. De volledige log <a href="/admin/logs.php" target="_blank">bekijk je hier</a>.</small>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    </script>
    <script type="text/javascript" src="js/filter.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#volzetTonen').on('change',function() {
          if($(this).prop("checked")) {
            $('#log tbody tr:not(.log-volzetVerklaring)').hide();
          } else {
            $('#log tbody tr').show();
          }
        });
      });
    </script>
  </body>
</html>
