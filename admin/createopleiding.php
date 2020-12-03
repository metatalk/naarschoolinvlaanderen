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
    <title>Naar School in Limburg</title>

  </head>
  <body>
    <?php
      require 'userbar.php';
      require 'functions/loadvestigingen.php';
      require 'functions/loadopleidingen.php';
      $vestiging = loadvestigingen($_GET['vestiging']);
      $fields = loadschema();
      $opleidingen = loadopleidingen($_GET['vestiging']);

      function huidigSchooljaar() {
        $currentMonth = date('n');
        $currentYear = date('Y');

        $schooljaar = $currentYear;

        if($currentMonth < 9 && $currentMonth >= 1) {
          $schooljaar = $currentYear - 1;
        }

        return $schooljaar;
      }

      function selectField($name,$type='existing',$id,$currentvalue=false,$vestigingtype) {

        switch($name) {
          case 'graad': $values = array('Eerste graad','Tweede graad','Derde graad','Se-n-Se','7e jaren','Onthaalonderwijs'); break;
          case 'opleiding': $values = array('Kleuteronderwijs', 'Lager Onderwijs','Onthaalonderwijs anderstaligen'); break;
          case 'leerjaar': $values = array('1ste leerjaar', '2de leerjaar'); break;
          case 'leerjaarlager': $values = array('1ste leerjaar', '2de leerjaar', '3de leerjaar', '4de leerjaar', '5de leerjaar', '6de leerjaar'); break;
        }

        if($name == 'leerjaarlager') {
          $i = huidigSchooljaar() - 5;
          for($i=huidigSchooljaar() - 5; $i < huidigSchooljaar(); $i++) {
            $values[] = 'Geboortejaar '.$i;
          }
        }

        $fieldname = $name == 'leerjaarlager' ? 'leerjaar' : $name;

        $graadField = '<select data-field="'.$fieldname.'" class="custom-select" name="'.$type.'['.$id.']['.$fieldname.']">';
        $graadField .= empty($currentvalue) ? '<option selected value="">Selecteer '.$fieldname.'</option>' : '<option value="">Selecteer '.$fieldname.'</option>';
        foreach($values as $value) {
          $selected = !empty($currentvalue) && $currentvalue == $value ? 'selected' : '';
          $graadField .= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
        }
        $graadField .= '</select>';
        return $graadField;
      }

    ?>
    <div class="container-fluid my-5">

      <?php if(!empty($_SESSION['saved']) && $_SESSION['saved'] == "yes"): $_SESSION['saved'] = ""; ?>
        <div class="pt-2 pb-5">
          <div class="alert alert-success" role="alert">
            De opleiding zijn succesvol opgeslaan.
          </div>
        </div>
      <?php endif; ?>

      <?php if(empty($vestiging) || !$_GET['vestiging']): ?>
        <div class="p-5">
          <div class="alert alert-danger" role="alert">
            Geen vestiging gevonden met id <?php echo $_GET['vestiging']; ?>.
          </div>
        </div>
      <?php else: ?>
        <?php
          if(!$isSuperAdmin && !in_array($_GET['vestiging'],$hasAccessTo)): ?>

          <div class="p-5">
            <div class="alert alert-danger" role="alert">
              Je hebt geen toegang om de opleidingen van deze vestiging te updaten.
            </div>
          </div>

        <?php else: ?>

          <div class="row">
            <div class="col-md-8">
              <h1 class="mb-2">Pas de opleidingen van <?= $vestiging->naam; ?> aan</h1>
              <p>
                <small>
                  <strong>Type: </strong>
                  <?= $vestiging->type == 'lager' ? 'kleuter- en lageronderwijs' : 'secundair onderwijs'; ?> &mdash;
                  <strong>Adres:</strong>
                  <?= $vestiging->adres; ?>, <?= $vestiging->postcode; ?> <?= $vestiging->gemeente; ?> &mdash;
                  <a href="createvestiging.php?id=<?= $vestiging->id; ?>">Gegevens vestiging aanpassen</a>  &mdash;
                  <a href="aanpassen.php?type=<?php echo $vestiging->type; ?>&vestiging=<?php echo $vestiging->id; ?>">Cijfers aanpassen</a>
                </small>
              </p>
            </div>
            <div class="col-md-4">
              <?php if(count($opleidingen) > 0 && $vestiging->type == 'secundair'): ?>
                <?php if(!empty($vestiging->vestigings_nummer)): ?>
                  <!--<p class="alert alert-primary text-center" style="font-size:0.8rem">
                    <a href="#" id="sync-onderwijskiezer" data-type="<?= $vestiging->type; ?>" data-id="<?= $vestiging->vestigings_nummer; ?>">
                      <strong><i class="fas fa-sync mr-2"></i><u>Synchroniseer opleidingen met onderwijskiezer.net</u></strong>
                    </a>
                  </p>-->
                <?php else: ?>
                  <p class="alert alert-primary">
                    <small>
                      <a href="createvestiging.php?id=<?= $vestiging->id; ?>">Vul het vestigingsnummer in</a> om je opleidingen te synchroniseren met onderwijskiezer.net.
                    </small>
                  </p>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>

          <?php if(count($opleidingen) == 0 && $vestiging->type == 'secundair'): ?>
            <?php if(!empty($vestiging->vestigings_nummer)): ?>
              <div class="bg-dark text-white pt-5 align-items-center text-center" style="height:500px" id="onderwijskiezer-alert">
                <h4 class="mt-5">Wil je opleidingen synchroniseren uit onderwijskiezer?</h4>
                <p class="lead" style="max-width:720px; margin: 20px auto;">Je kan om te starten alle opleidingen voor deze vestigingen in laden uit de database van onderwijskiezer.net. Op deze manier moet je niet alle opleiding manueel invullen.</p>
                <p class="mt-3">
                  <a href="#" id="sync-onderwijskiezer" data-type="<?= $vestiging->type; ?>" data-id="<?= $vestiging->vestigings_nummer; ?>" class="btn btn-lg btn-primary">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Opleiding inladen uit onderwijskiezer
                  </a>
                </p>
              </div>
            <?php else: ?>
              <p class="alert alert-primary">
                <small>
                  <a href="createvestiging.php?id=<?= $vestiging->id; ?>">Vul het vestigingsnummer in</a> om je opleidingen te synchroniseren met onderwijskiezer.net.
                </small>
              </p>
            <?php endif; ?>
          <?php endif; ?>

          <form method="post" id="opleidingen-form" class="mt-3" action="functions/saveopleiding.php">
            <input type="hidden" name="type" value="<?= $vestiging->type; ?>">
            <input type="hidden" name="vestiging_id" value="<?= $vestiging->type; ?>">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <?php foreach($fields as $fieldname => $type): ?>
                      <th class="align-top <?= $fieldname == 'id' || $fieldname == 'vestiging_id' || $fieldname == 'graadjaar' ? 'd-none' : ''; ?>" style="<?= $fieldname == 'administratieve_code' ? 'max-width:200px' : ''; ?>">
                        <?php
                          $fieldname = str_replace('_',' ',$fieldname);
                          $fieldname = str_replace('code', 'groep',$fieldname);

                          echo '<span class="text-capitalize">'.$fieldname.'</span>';

                          if($fieldname === 'administratieve groep') {
                            echo '<i class="ml-2 fas fa-info-circle" data-toggle="tooltip" data-placement="left" title="Meerdere administratieve groepen ingeven is mogelijk met een komma. Bv. 392,291,300"></i>';
                          }
                          if($fieldname === 'leerjaar') {
                            echo '<small> (Optioneel)</small>';
                          }
                        ?>
                      </th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody id="opleidingen-wrapper">
                  <?php foreach($opleidingen as $opleiding): ?>
                    <tr>
                      <?php foreach($opleiding as $fieldname => $value): ?>
                        <td class="<?= $fieldname == 'id' || $fieldname == 'vestiging_id' || $fieldname == 'graadjaar' ? 'd-none' : ''; ?>">
                          <?php if($fieldname == 'graad'): ?>
                            <?= selectField('graad','existing',$opleiding->id,$value,$vestiging->type); ?>
                          <?php elseif($fieldname == 'opleiding' && $vestiging->type == 'lager'): ?>
                            <?= selectField('opleiding','existing',$opleiding->id,$value,$vestiging->type); ?>
                          <?php elseif($fieldname == 'leerjaar' && $vestiging->type == 'secundair'): ?>
                            <?= selectField('leerjaar','existing',$opleiding->id,$value,$vestiging->type); ?>
                          <?php elseif($fieldname == 'leerjaar' && $vestiging->type == 'lager'): ?>
                            <?= selectField('leerjaarlager','existing',$opleiding->id,$value,$vestiging->type); ?>
                          <?php else: ?>
                            <input <?= $fieldname == 'administratieve_code' || $fieldname == 'opleiding' ? 'required' : false; ?> class="form-control" type="text" name="existing[<?= $opleiding->id ?>][<?= $fieldname ?>]" value="<?= $value ?>">
                          <?php endif; ?>
                        </td>
                      <?php endforeach; ?>
                      <td>
                        <a href="functions/deleteopleiding.php?id=<?= $opleiding->id ?>&type=<?= $vestiging->type ?>">Verwijderen</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>

                    <tr class="nieuweopleiding <?= count($opleidingen) > 0 ? 'd-none' : ''; ?>" data-index="1">
                      <?php foreach($fields as $fieldname => $type): ?>
                        <td class="<?= $fieldname == 'id' || $fieldname == 'vestiging_id' || $fieldname == 'graadjaar' ? 'd-none' : ''; ?>">
                          <?php if($fieldname == 'graad'): ?>
                            <?= selectField('graad','new',1,false,$vestiging->type); ?>
                          <?php elseif($fieldname == 'opleiding' && $vestiging->type == 'lager'): ?>
                            <?= selectField('opleiding','new',1,false,$vestiging->type); ?>
                          <?php elseif($fieldname == 'leerjaar' && $vestiging->type == 'secundair'): ?>
                            <?= selectField('leerjaar','new',1,false,$vestiging->type); ?>
                          <?php elseif($fieldname == 'leerjaar' && $vestiging->type == 'lager'): ?>
                            <?= selectField('leerjaarlager','new',1,false,$vestiging->type); ?>
                          <?php else: ?>
                            <input <?= $fieldname == 'administratieve_code' || $fieldname == 'opleiding' ? 'required' : false; ?> class="form-control" type="text" data-field="<?= $fieldname ?>" name="new[1][<?= $fieldname ?>]" value="<?= $fieldname == 'vestiging_id' ? $vestiging->id : ''; ?>">
                          <?php endif; ?>
                        </td>
                      <?php endforeach; ?>
                      <td>
                        <a href="#" class="removenewrow">Verwijder</a>
                      </td>
                    </tr>

                </tbody>
              </table>
            </div>
            <button type="button" id="nieuwe-opleiding" class="btn btn-lg btn-success mr-3"><i class="fas fa-plus"></i> Nieuwe rij toevoegen</button>
            <button type="submit" id="opslaan" class="btn btn-lg btn-primary">Alles opslaan</button>
          </form>

        <?php endif; ?>
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
    <script type="text/javascript" src="js/opleidingen.js"></script>

    <script id="input-row" type="text/x-handlebars-template">



    </script>


  </body>
</html>
