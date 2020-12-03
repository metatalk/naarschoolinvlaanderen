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
    <?php
      session_start();
      require 'userbar.php';
    ?>
    <div class="container">

      <?php if(!empty($_SESSION['success'])): ?>
        <div class="pt-2 pb-5">
          <div class="alert alert-success" role="alert">
            De gebruiker is succesvol opgeslaan.
          </div>
        </div>
      <?php $_SESSION['success'] = ""; endif; ?>

      <div class="row">
        <div class="col-md-8">
          <h1>Gebruikers</h1>
        </div>
        <div class="col-md-4 text-right">
          <a href="createuser.php" class="btn btn-primary">Nieuwe gebruiker toevoegen</a>
        </div>
      </div>

      <form action="/admin/users.php" method="get">

        <div class="row bg-light mt-4 text-white pt-3 pb-3">
          <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Zoek op gebruikers">
          </div>
          <div class="col-md-4">
            <select class="custom-select" name="field">
              <option value="given_name">Zoek op voornaam</option>
              <option value="family_name">Zoek op achternaam</option>
              <option value="email">Zoek op e-mail</option>
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-secondary" name="button">Zoek</button>
          </div>
        </div>

      </form>

      <?php

        $currentPage = !empty($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 0;
        $searchParams = array('per_page' => 50, 'sort' => 'created_at:-1', 'page' => $currentPage, 'include_totals' => true);

        if(!empty($_GET['search']) && !empty($_GET['field'])) {
          $searchParams['q'] = $_GET['field'].':*'.$_GET['search'].'*';
        }

        $results = $mgmt_api->users->getAll($searchParams);
        $pagesNeeded = ceil($results['total'] / $results['limit']);

      ?>

      <?php if($isSuperAdmin): ?>
        <nav aria-label="Page navigation" class="mt-4">

          <div class="d-flex flex-row mt-4 align-items-center">
            <div class="pr-2">
              Pagina:
            </div>
            <ul class="pagination mb-0">
              <?php for($i=1;$i<=$pagesNeeded;$i++): ?>
              <li class="page-item <?php if($i-1 == $currentPage) { echo 'active'; } ?>"><a class="page-link" href="/admin/users.php?page=<?= $i-1 ?>"><?= $i ?></a></li>
              <?php endfor; ?>
            </ul>
            <div class="pl-5 text-right">
              <small>Totaal aantal gebruikers: <?= $results['total'] ?> &mdash; (Er worden 50 resultaten per pagina getoond).</small>
            </div>
          </div>

        </nav>
      <?php endif; ?>

      <?php if(!empty($_GET['search']) && !empty($_GET['field'])): ?>
          <?php if($results['total'] > 0): ?>
            <div class="alert alert-success mt-3" role="alert">
              <?= $results['total'] ?> resultaten voor <?= $_GET['search'] ?> &mdash; <a href="/admin/users.php">Alle gebruikers weergeven</a>
            </div>
          <?php else: ?>
            <div class="alert alert-danger mt-4" role="alert">
              Geen resultaten voor <?= $_GET['search'] ?> &mdash; <a href="/admin/users.php">Alle gebruikers weergeven</a>
            </div>
          <?php endif; ?>
      <?php endif; ?>

      <table class="table mt-5">
        <tbody id="users">
        <?php

        include 'functions/loadvestigingen.php';

        if (! empty($results['users'])) {

          foreach ($results['users'] as $result) {
            if($result['user_metadata']['belongsTo'] == $userData['user_id'] || $userData['user_id'] == $result['user_id'] || $isSuperAdmin) {
              if(!empty($result['user_metadata']['school'])) {
                  $vestigingen = loadvestigingen(false,array('id'=>$result['user_metadata']['school']));
              }
              echo '<tr data-filtertext="'.strtolower($result['given_name'] . ' ' . $result['family_name'] . ' '. $result['email']).'">';
              echo '<td><h5>'.$result['given_name'].' ' . $result['family_name'] .'</h5>';
              echo '<p>'.$result['email'].'</p>';
              echo '<p><small>Aangemaakt op: '.substr($result['created_at'],0,10).'</small></p>';
              echo '<p style="max-width:700px"><small> Beheert: ';
              foreach($vestigingen as $key => $vestiging) {
                echo $vestiging->naam;
                echo ' &middot; ';
              }
              echo '</small></p>';
              echo '</td>';
              echo '<td class="text-right"><a href="createuser.php?userid='.$result['user_id'].'">Aanpassen</a> &mdash; <a href="deleteuser.php?userid='.$result['user_id'].'" onclick="if(!confirm(\'Ben je zeker dat je deze gebruiker wil verwijderen?\')) return false;">Verwijderen</a></td>';
              echo '</tr>';
            }
          }
        }
        ?>
        </tbody>
      </table>


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
