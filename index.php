<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="css/jquery.dropdown.css">
    <link rel="stylesheet" href="css/main.css">
    <title>Naar School in Vlaanderen</title>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-153805386-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-153805386-1');
    </script>

  </head>
  <body class="<?php if(!empty($_GET['type']) && $_GET['type'] == 'secundair'): ?>app-secundair<?php else: ?>app-lager<?php endif; ?>">

    <?php require __DIR__.'/admin/checklogin.php'; ?>

    <?php if(!empty($userInfo) && $_GET['mode'] == 'export'): ?>
      <div class="bg-warning p-3">
        Je kan nu exporteren. Filter op de gegevens die je wil exporteren en klik vervolgens op de export knop.
        <a href="#" class="btn btn-primary btn-sm ml-4" id="exportresults">Resultaten exporteren</a>
      </div>
    <?php endif; ?>

    <div class="container-fluid app-wrapper">
      <a href="#" class="d-md-none toggle-filter position-fixed px-3 py-2 bg-success text-white" style="top:20px; left: 0; z-index:100;"><i class="fas fa-arrow-left"></i> Wijzig filter</a>
      <div class="row">
        <div class="col-md-4 app-filter active p-4 p-md-5">

          <h1 class="h3">Zoek vrije plaatsen</h1>

          <div class="anno-container">

            <div class="app-filtergroup mt-5" data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="manual" data-content="Start door te selecteren voor welk type onderwijs je beschikbare plaatsen wil zoeken.">
              <p class="app-filtertitle">Selecteer het onderwijsniveau</p>
              <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-primary text-left">
                  <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                  <input type="radio" data-filter="typeonderwijs" name="typeonderwijs" value="lager" autocomplete="off"><strong>Kleuter en lager</strong>
                </label>
                <label class="btn btn-primary text-left">
                  <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                  <input type="radio" data-filter="typeonderwijs" name="typeonderwijs" value="secundair" autocomplete="off"><strong>Secundair</strong>
                </label>
              </div>
            </div>

            <div class="app-filtergroup app-filtergroup-disabled mt-4" id="filter-schooljaar">
              <p class="app-filtertitle">Voor welk schooljaar zoek je plaatsen?</p>
              <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-primary text-left active">
                  <input type="radio" name="schooljaar" value="2020" autocomplete="off">Schooljaar <br> 2020 - 2021
                </label>
                <label class="btn btn-primary text-left">
                  <input type="radio" name="schooljaar" value="2021" autocomplete="off">Schooljaar <br> 2021 - 2022
                </label>
              </div>
            </div>

            <div class="app-filtergroup app-filtergroup-disabled mt-4" id="filter-gemeente">
              <p class="app-filtertitle">Gemeente</p>
              <select disabled multiple data-filter="gemeente" class="custom-select custom-select-lg"></select>
              <small class="form-text text-muted">
                Selecteer de gemeenten waar je je kind(eren) graag naar school laat gaan.
              </small>
            </div>
          </div>

          <div class="<?php if(!empty($_GET['type']) && $_GET['type'] == 'secundair'): ?>d-flex flex-column-reverse<?php endif; ?>">
            <div class="app-filtergroup app-filtergroup-disabled mt-4" id="filter-opleiding">
              <p class="app-filtertitle">Opleiding <?php if(!empty($_GET['type']) && $_GET['type'] == 'lager'): ?><i class="ml-2 fas fa-info-circle" data-toggle="tooltip" data-placement="left" title="De vrije plaatsen voor anderstalige nieuwkomers zijn enkel beschikbaar indien er voor het betrokken geboortejaar of leerjaar ook vrije plaatsen zijn."></i><?php endif; ?></p>
              <select disabled data-filter="opleiding" class="custom-select custom-select-lg"></select>
            </div>

            <div class="app-filtergroup app-filtergroup-disabled mt-4" id="filter-leerjaar">
              <p class="app-filtertitle">Leerjaar <?= $_GET['type'] == 'lager' ? ' of geboortejaar' : ''; ?></p>
              <select disabled data-filter="leerjaar" class="custom-select custom-select-lg"></select>
            </div>
          </div>





          <button disabled class="d-md-none mt-5 mb-5 toggle-filter btn btn-success btn-lg">Toon <span class="title-count"></span> resultaten</button>
          <p class="d-md-none pb-3"></p>
        </div>


        <div class="col-md-8 bg-light app-results p-4 p-md-5">
          <div class="row align-middle pt-5 pt-md-0">
            <div class="col-md-12 pt-5 pt-md-0">
              <h1 class="h6 app-title mb-5">
                <span id="title-count" class="title-count badge badge-dark"></span>
                Resultaten <span id="show-title-opleiding">voor <strong id="title-opleiding">richting</strong></span>
                <span id="show-title-gemeente">in <strong id="title-gemeente">Hasselt</strong></span>
                <small id="show-indicator-leerlingen">&mdash; Indicator leerlingen</small>
              </h1>
            </div>
          </div>

          <div id="noresults" class="p-5 text-center">
            <p class="h3">Geen resultaten gevonden voor je zoekopdracht.</h3>
          </div>

          <div class="" id="app-results"></div>

          <div class="d-flex flex-column justify-content-center align-items-center app-startscreen app-startscreen-show">
            <div class="text-center" style="max-width: 500px">
              <h2 class="mb-4">Ontdek in welke scholen nog vrije plaatsen beschikbaar zijn</h2>
              <p class="lead">Op deze website kan je bij de deelnemende gemeenten zoeken naar vrije plaatsen.</p>
              <ul class="list-unstyled">
                <li class="mb-2"><strong>Stap 1:</strong> Selecteer aan de linkerkant het onderwijsniveau: kleuter- en lager of secundair onderwijs.</li>
                <li class="mb-2"><strong>Stap 2:</strong> Selecteer het schooljaar</li>
                <li class="mb-2"><strong>Stap 3:</strong> Selecteer de gemeente(s)</li>
                <li><strong>Stap 4:</strong> Filter op leerjaren en opleidingen</li>
              </ul>
            </div>
            <div class="position-absolute p-3" style="bottom:0;">
              <small>Deze applicatie is mogelijk gemaakt door <a href="//sint-truiden.be">Stad Sint-Truiden<a/>, <a href="http://www.naarschoolinsinttruiden.be/basisonderwijs/website15/">LOP Sint-Truiden</a> en <a href="//onderwijskiezer.be">Onderwijskiezer.be</a></small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php if($userInfo): ?>
      <a class="d-none download-export position-fixed px-3 py-2 bg-warning text-white" style="top:20px; right: 0; z-index:100;"></i> Download export</a>
    <?php endif; ?>
    <script>
      var $buoop = {
        required:{e:-5,f:-3,o:-3,s:-1,c:-3},
        insecure:true,
        api:2020.02,
        text: {
           'msg':'Je web browser ({brow_name}) is niet meer up-to-date.',
           'msgmore': 'We kunnen geen correcte werking garanderen voor deze browser. Update je browser voor een veiligere, snellere en betere ervaring op deze website.',
           'bupdate': 'Update browser',
           'bignore': 'Negeren',
           'remind': 'Je krijgt hiervan een herinnering binnen {days} dagen.',
           'bnever': 'Nooit meer tonen.'
        }
      };
      function $buo_f(){
       var e = document.createElement("script");
       e.src = "//browser-update.org/update.min.js";
       document.body.appendChild(e);
      };
      try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
      catch(e){window.attachEvent("onload", $buo_f)}
    </script>

    <script type="text/javascript">
      var appUrl = '<?= isset($_SERVER['HTTPS']) ? 'https://' : 'http://'; ?><?= $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0] ?>';
    </script>

    <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.min.js"></script>
    <script type="text/javascript" src="js/jquery.scrollintoview.min.js"></script>
    <script type="text/javascript" src="js/jquery.dropdown.min.js"></script>
    <script type="text/javascript" src="js/jlinq.js"></script>
    <?php if(!empty($userInfo) && $_GET['mode'] == 'export'): ?>
      <script type="text/javascript" src="js/export.js"></script>
    <?php endif; ?>
    <script type="text/javascript" src="js/main_v2.js"></script>


    <!-- Templates -->
    <script id="input-school" type="text/x-handlebars-template">
      <div class="card shadow card-school w-100 mb-5">
        <div class="row no-gutters p-4">
          <div class="col-12">
            <h4 class="mt-2 mb-4">{{naam}}</h4>
          </div>
          <div class="col-md-8">
            <p class="mb-0">{{adres}} <br> {{postcode}} {{gemeente}}</p>
          </div>
          {{#if website}}
          <div class="col-md-4">
            <p><a href="{{website}}" target="_blank"><i class="fas fa-link pr-2"></i>{{website}}</a></p>
          </div>
          {{/if}}
        </div>
        <div class="table-responsive-md">
          <!--<h6 class="pl-4 pb-2"><i class="fas fa-graduation-cap"></i> Opleidingen</h6>-->
          <table class="table table-striped mb-0">
            {{#each opleidingen}}
              {{#unless this.hide}}
              <tr class="{{#if this.issecundairhoger }}secundairhoger{{/if}}" data-opleiding="{{#if this.administratieve_code}}{{ this.administratieve_code }}{{else}}{{ this.opleiding }}{{/if}}" data-leerjaar="{{{trimString this.leerjaar }}}">
                <td class="pl-4">
                  <strong>{{ this.opleiding }}</strong> {{#if this.leerjaar}} &mdash; {{this.leerjaar}}  {{/if}}
                </td>
                <td class="pr-4 text-right">
                  {{#if this.cijfers}}
                    {{#each this.cijfers}}
                      {{#if this.volzet}}
                        <span class="scholen-item-badge badge badge-danger" data-toggle="tooltip" title="">Volzet <i class="fas fa-info-circle"></i></span>
                        <small class="d-block text-muted mt-2">
                          Volzet op {{datum}}
                        </small>
                      {{else}}
                        {{#if this.plaatsen}}
                          <span class="scholen-item-badge badge badge-success">{{this.vrijeplaatsentotaal}} plaatsen</span>
                          {{#if this.percentageindtonen}}
                            <small class="d-block mt-1">{{{this.indmessage}}} <i class="ml-2 fas fa-info-circle" data-toggle="tooltip" data-placement="left" title="Een leerling waarvan het gezin minstens één selectieve participatietoeslag, schooltoelage ontvangt of een beperkt inkomen heeft of waarvan de moeder geen diploma secundair onderwijs heeft."></i></small>
                          {{/if}}
                          <small class="d-block text-muted mt-2">
                            laatst aangepast op {{datum}}
                          </small>
                        {{else}}
                          <span class="showonsecundairhoger scholen-item-badge badge badge-success">Nog plaats</span>
                          <em class="hideonsecundairhoger"><small>Nog geen cijfers beschikbaar.</small></em>
                        {{/if}}
                      {{/if}}
                    {{/each}}
                  {{else}}
                  <span class="showonsecundairhoger scholen-item-badge badge badge-success">Nog plaats</span>
                  <em class="hideonsecundairhoger"><small>Nog geen cijfers beschikbaar.</small></em>
                  {{/if}}
                </td>
              </tr>
              {{/unless}}
            {{/each}}
          </table>
        </div>
      </div>
    </script>

    <div id="loader" class="position-fixed w-100 h-100 overlay d-flex justify-content-center align-items-center flex-column text-white">
      <div class="spinner-border text-success" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
      </div>
      <h4 class="mt-3">Even geduld, de cijfers voor je selectie worden geladen.</h4>
    </div>


  </body>
</html>
