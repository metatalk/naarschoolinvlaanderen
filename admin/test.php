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
      require 'userbar.php';
      require 'functions/loadvestigingen.php';
      require 'functions/loadopleidingen.php';
      $vestiging = loadvestigingen($_GET['vestiging']);
      $opleidingen = loadopleidingenAjax($_GET['vestiging']);
    ?>

    <script type="text/javascript">
      var opleidingen = <?= $opleidingen; ?>
    </script>

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
          echo '<script type="text/javascript">var instelling = '.$_GET['vestiging'].'; var onderwijstype = "'.$_GET['type'].'";</script>';
        } else {
          die('Geen instelling en/of onderwijstype opgegeven.');
        }

      ?>
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
            <li class="nav-item">
              <a class="nav-link active" id="set2019" data-schooljaar="2019">2019-2020</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="set2020" data-schooljaar="2020">2020-2021</a>
            </li>
          </ul>
          <h6>Vul hieronder de cijfers in <span id="schooljaarIndicator"></span>.</h6>
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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.min.js"></script>
    <script type="text/javascript" src="jlinq.js"></script>
    <script type="text/javascript" src="js/_test.js"></script>

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
        <div class="form-check">
          <input class="form-check-input" {{#if hide}}checked{{/if}} type="checkbox" id="verbergen" value="ja" data-update="hide" data-key="{{id}}">
          <label class="form-check-label" for="verbergen"  style="font-weight:normal;">
            Verbergen voor ouders
          </label>
        </div>
        </div>
        <div class="col-md-2 mb-3">
          <div class="updatePlaatsen-loader">
            <span class="spinner-border spinner-border-sm text-primary" data-toggle="tooltip" title="Je input wordt gevalideerd en opgeslaan." role="status" aria-hidden="true"></span>
            <input data-update="plaatsen" class="form-control" {{#if disableInd}}disabled{{/if}} data-key="{{id}}" type="number" value="{{plaatsen}}" data-indplaatsen="{{percentageindaantal}}">
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
                <input data-update="plaatsenbezet" class="form-control" data-key="{{id}}" type="number" value="{{plaatsenbezet}}">
              </div>
              <!--<small class="updatePlaatsen-vrijeplaatsen">{{#if vrijeplaatsen}}{{vrijeplaatsen}} vrije plaatsen{{/if}}</small>-->
            </div>
          </div>
          <small class="updatePlaatsen-vrijeplaatsen text-center">{{#if vrijeplaatsentotaal}} In totaal nog {{vrijeplaatsentotaal}} vrije plaatsen.{{/if}}</small>
        </div>
        <div class="col-md-1 mb-3">
          <div class="form-check">
            <input class="form-check-input" data-update="volzet" data-key="{{id}}" {{#if volzet}}checked{{/if}} type="checkbox" value="ja" id="volzetVerklaring{{id}}">
            <label class="form-check-label" for="volzetVerklaring{{id}}">
              Capaciteit bereikt
            </label>
          </div>
        </div>
        <div class="plaatsen-messenger"></div>
      </div>

    </script>

  </body>
</html>
