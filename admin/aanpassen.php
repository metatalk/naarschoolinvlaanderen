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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <link rel="stylesheet" href="css/main.css">
    <title>Naar School in Vlaanderen</title>

  </head>
  <body>
    <?php
      require 'userbar.php';
      require 'functions/loadvestigingen.php';
      require 'functions/loadopleidingen.php';
      include 'functions/loadschooljaren.php';
      $vestiging = loadvestigingen($_GET['vestiging']);
      $opleidingen = loadopleidingen($_GET['vestiging']);
      $schooljaren = explode(',',loadschooljaren()->admin);
    ?>

    <?php
      if(!$isSuperAdmin && !in_array($_GET['vestiging'],$hasAccessTo)): ?>

      <div class="p-5">
        <div class="alert alert-danger" role="alert">
          Je hebt geen toegang om cijfers van deze instelling aan te passen.
        </div>
      </div>

    <?php else: ?>
      <?php
        if(!empty($_GET['vestiging']) && $_GET['type'] != "lager" || $_GET['type'] != "secundair") {
          $selectedSchooljaar = !empty($_GET['schooljaar']) ? $_GET['schooljaar'] : '';
          echo '<script type="text/javascript">var instelling = '.$_GET['vestiging'].'; var onderwijstype = "'.$_GET['type'].'"; var vestigingsnaam = "'.$vestiging->naam.'"; var schooljaar = "'.$selectedSchooljaar.'";</script>';
        } else {
          die('Geen instelling en/of onderwijstype opgegeven.');
        }

      ?>

      <?php if(empty($_GET['schooljaar'])): ?>

        <div class="modal w-100 fade" id="schooljaarmodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header  px-4 ">
                <h5 class="modal-title" id="staticBackdropLabel">Voor welk schooljaar wil je de cijfers aanpassen?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body  p-4 ">
                <p>Klik hieronder op het schooljaar waarvoor je de cijfers van <?= $vestiging->naam; ?> wil aanpassen.</p>

                <div class="">
                  <?php foreach($schooljaren as $schooljaar): ?>
                    <a class="btn btn-lg btn-primary" href="/admin/aanpassen.php?type=<?= $_GET['type'] ?>&vestiging=<?= $_GET['vestiging'] ?>&schooljaar=<?= $schooljaar ?>"><?= $schooljaar; ?>-<?= (int) $schooljaar + 1; ?></a>
                  <?php endforeach; ?>
                </div>

              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="container-fluid p-md-5">
        <h2>Cijfers van <?= $vestiging->naam; ?>  aanpassen</h2>
        <a href="createvestiging.php?id=<?= $vestiging->id; ?>">Gegevens aanpassen</a> &mdash;
        <a href="createopleiding.php?type=<?php echo $vestiging->type; ?>&vestiging=<?php echo $vestiging->id; ?>">Opleidingen aanpassen</a>
        <div class="loader loaderStep2">
          <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
          <span class="generalLoader-text">Even geduld, de verschillende studierichtingen worden geladen</span>
        </div>
        <div class="d-none p-3 bg-danger mt-5 text-white">
          Je hebt nog geen <a class="text-white" href="createopleiding.php?type=<?php echo $vestiging->type; ?>&vestiging=<?php echo $vestiging->id; ?>"><u>opleidingen toegevoegd</u></a>.
        </div>
        <div id="optie-wrapper" class="pt-3" style="display:block">
          <ul class="nav nav-tabs mb-4 selectSchooljaar">

            <?php foreach($schooljaren as $schooljaar): ?>
              <li class="nav-item">
                <a class="nav-link <?= !empty($_GET['schooljaar']) && $schooljaar == $_GET['schooljaar'] ? 'active text-success font-weight-bold' : ''; ?>" data-schooljaar="<?= $schooljaar; ?>" href="aanpassen.php?type=<?= $vestiging->type; ?>&vestiging=<?= $vestiging->id; ?>&schooljaar=<?= $schooljaar; ?>"><?= $schooljaar; ?>-<?= (int) $schooljaar + 1; ?></a>
              </li>
            <?php endforeach; ?>
          </ul>

          <?php if($isSuperAdmin === true || $canEditVestigingen === true): ?>
            <div class="text-right">
              <a href="#" id="export" class="btn btn-sm btn-primary disabled" role="button" aria-disabled="true"><i class="fas fa-download"></i>&nbsp; Exporteren</a>
            </div>
          <?php endif; ?>

          <div class="form-row optiesHeader">
            <div class="col-md-3 mb-3">
              Opleiding / richting
            </div>
            <div class="col-md-2 mb-3">
              Capaciteit
            </div>
            <div class="col-md-2 mb-2">
              <?php if($_GET['type'] != "secundair"): ?>
                Percentage IND
              <?php else: ?>
                Percentage IND
              <?php endif; ?>
            </div>
            <div class="col-md-2 mb-2">
              <?php if($_GET['type'] != "secundair"): ?>
                Zittende leerlingen IND
              <?php else: ?>
                Zittende leerlingen IND
              <?php endif; ?>
            </div>
            <div class="col-md-2 mb-2">
              <?php if($_GET['type'] != "secundair"): ?>
                Zittende leerlingen NIND
              <?php else: ?>
                Zittende leerlingen
              <?php endif; ?>
            </div>
            <div class="col-md-1 mb-2">
              Capaciteit bereikt
            </div>
          </div>
          <div id="optie-list">

          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.min.js"></script>
    <script type="text/javascript" src="jlinq.js"></script>
    <script type="text/javascript" src="js/inputmask.js"></script>
    <script type="text/javascript" src="js/aanpassen.js"></script>

    <!-- Templates -->
    <script id="input-row" type="text/x-handlebars-template">

      <div class="form-row plaatsen-row {{#if volzet}}is-volzet{{/if}}" data-key="{{id}}" data-plaatsen="{{plaatsen}}" data-school="<?= $vestiging->naam; ?>" data-opleiding="{{opleiding}} {{graad}} {{leerjaar}}">
        <div class="col-md-3 mb-3">
          <label>{{opleiding}}
            <small>
            <?php if($_GET['type'] == "secundair"): ?>
              {{graad}} - {{leerjaar}}
            <?php else: ?>
              {{leerjaar}}
            <?php endif; ?>
          </small>
        </label>
        <div class="form-check mt-3">
          <input class="form-check-input" {{#if hide}}checked{{/if}} type="checkbox" id="verbergen" value="ja" data-update="hide" data-key="{{id}}">
          <label class="form-check-label" for="verbergen"  style="font-weight:normal;">
            Verbergen voor ouders indien deze richting niet wordt aangeboden voor dit specifieke schooljaar
          </label>
        </div>
        </div>
        <div class="col-md-2 mb-3">
          <div class="updatePlaatsen-loader">
            <span class="spinner-border spinner-border-sm text-primary" data-toggle="tooltip" title="Je input wordt gevalideerd en opgeslaan." role="status" aria-hidden="true"></span>
            <input data-update="plaatsen" class="form-control" data-key="{{id}}" type="number" value="{{plaatsen}}" data-indplaatsen="{{percentageindaantal}}">
          </div>
          <small class="updatePlaatsen-indicatorAantal">{{#if percentageind}}Waarvan {{percentageindaantal}} indicator leerlingen.{{/if}}</small>
        </div>
        <div class="col-md-2 mb-3">
          <div class="updatePlaatsen-loader">
            <span class="spinner-border spinner-border-sm text-primary" data-toggle="tooltip" title="Je input wordt gevalideerd en opgeslaan." role="status" aria-hidden="true"></span>
            <div class="input-group">
              <input data-update="percentageind" class="form-control" {{#unless plaatsen}}disabled{{/unless}} {{#if disableInd}}disabled{{/if}} data-key="{{id}}" type="number" value="{{#if percentageind}}{{comma percentageind}}{{/if}}" step="0.01" min="0" max="100">
              <div class="input-group-append">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>

          <div class="form-check" {{#if percentageind}}style="display:block"{{else}}style="display:none"{{/if}}  id="percentageindtonen">
            <input class="form-check-input" {{#if percentageindtonen}}checked{{/if}} type="checkbox" value="ja" data-update="percentageindtonen" data-key="{{id}}">
            <label class="form-check-label" for="percentageindtonen">
              Tonen
            </label>
          </div>

        </div>
        <div class="col-md-4 mb-3">
          <div class="row">
            <div class="col-md-6">
              <div class="updatePlaatsen-loader">
                <span class="spinner-border spinner-border-sm text-primary" data-toggle="tooltip" title="Je input wordt gevalideerd en opgeslaan." role="status" aria-hidden="true"></span>
                <input data-update="plaatsenbezetind" class="form-control" {{#if disableInd}}disabled{{/if}} data-key="{{id}}" type="number" value="{{plaatsenbezetind}}">
              </div>
            </div>
            <div class="col-md-6 pl-0">
              <div class="updatePlaatsen-loader">
                <span class="spinner-border spinner-border-sm text-primary" data-toggle="tooltip" title="Je input wordt gevalideerd en opgeslaan." role="status" aria-hidden="true"></span>
                <input data-update="plaatsenbezet" class="form-control" data-key="{{id}}" {{#if disableInd}}disabled{{/if}} type="number" value="{{plaatsenbezet}}">
              </div>
              <!--<small class="updatePlaatsen-vrijeplaatsen">{{#if vrijeplaatsen}}{{vrijeplaatsen}} vrije plaatsen{{/if}}</small>-->
            </div>
          </div>
          <small class="updatePlaatsen-vrijeplaatsen text-center">{{#if vrijeplaatsentotaal}} In totaal nog {{vrijeplaatsentotaal}} vrije plaatsen.{{/if}}</small>
        </div>
        <div class="col-md-1 mb-3">
          <div class="form-check">
            <input class="form-check-input toggle-volzet" {{#if volzet}}checked{{/if}} value="{{ volzet }}" type="checkbox" data-key="{{id}}" data-target="#volzetVerklaringModal{{id}}" id="volzetVerklaring{{id}}">
            <label class="form-check-label" for="volzetVerklaring{{id}}">
              Capaciteit bereikt
            </label>
          </div>
          <p class="mt-2" data-volzetdatum="{{ volzet }}">
            <small>{{ volzet }}</small>
          </p>
        </div>
        <div class="plaatsen-messenger"></div>

        <div class="modal fade" id="volzetVerklaringModal{{id}}" tabindex="-1" role="dialog" aria-labelledby="volzetVerklaringModal{{id}}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" >Op welke datum is de capaciteit bereikt?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <label>Datum en uur. <strong>bv. 01/11/2020 09:50</strong></label>
                <input class="form-control" id="volzetDatum{{id}}" type="text">
                <small id="passwordHelpBlock" class="form-text text-muted">
                  Een volzetverklaring wordt alleen geregistreerd als je de datum en tijdstip van de volzetverklaring invult.
                </small>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-primary registerVolzet" id="registerVolzet{{id}}" disabled data-key="{{id}}">Opslaan</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </script>

  </body>
</html>
