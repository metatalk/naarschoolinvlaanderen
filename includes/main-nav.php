<div class="main-nav">

  <div class="margin-medium">
    <a href="#" class="inverse-underline" onclick="resetForm()">Reset formulier</a>
  </div>

  <form id="main-form" class="margin-mini">

    <!-- Schooljaar -->
    <div class="margin-medium margin-border">
      <h2>Schooljaar</h2>
      <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-secondary active text-left">
          <input type="radio" name="schooljaar" value="schooljaar-huidig" autocomplete="off"><strong>Dit schooljaar</strong><br/>2018 - 2019
        </label>
        <label class="btn btn-secondary text-left">
          <input type="radio" name="schooljaar" value="schooljaar-volgend" autocomplete="off"><strong>Volgend schooljaar</strong><br/>2019 - 2020
        </label>
      </div>
    </div>

    <!-- Niveau -->
    <div class="margin-medium margin-border">
      <h2>Niveau</h2>
      <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-secondary active text-left">
          <input type="radio" name="niveau" value="niveau-kleuter" autocomplete="off">Kleuter
        </label>
        <label class="btn btn-secondary text-left">
          <input type="radio" name="niveau" value="niveau-lager" autocomplete="off">Lager
        </label>
        <label class="btn btn-secondary text-left">
          <input type="radio" name="niveau" value="niveau-secundair" autocomplete="off">Secundair
        </label>
      </div>
    </div>

    <!-- Gewoon / buitengewoon -->
    <div class="margin-medium margin-border">
      <h2>Gewoon / buitengewoon onderwijs</h2>
      <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-secondary active text-left">
          <input type="radio" name="onderwijstype" value="onderwijs-gewoon" autocomplete="off">Gewoon
        </label>
        <label class="btn btn-secondary text-left">
          <input type="radio" name="onderwijstype" value="onderwijs-buitengewoon" autocomplete="off">Buitengewoon
        </label>
      </div>
    </div>

    <!-- Richting -->
    <div class="margin-medium margin-border">
    <h2>Richting</h2>
      <div class="form-group">
        <input type="text" class="form-control margin-small" id="richting-zoek" placeholder="Zoek doorheen richtingen...">
        <ul class="list-unstyled">
          <li class="row margin-mini">
            <label class="form-check-label col-10" for="richting-1A">
              1A<br/>
              <small>Lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor</small>
            </label>
            <div class="col-2">
              <input class="form-check-input float-right w-auto" type="checkbox" name="richting" value="richting-1A" style="position:inherit;">
            </div>
          </li>
          <li class="row margin-mini">
            <label class="form-check-label col-10" for="richting-1B">
              1B<br/>
              <small>Lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor</small>
            </label>
            <div class="col-2">
              <input class="form-check-input float-right w-auto" type="checkbox" name="richting" value="richting-1B" style="position:inherit;">
            </div>
          </li>
          <li class="row margin-mini">
            <label class="form-check-label col-10" for="richting-1C">
              1C<br/>
              <small>Lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor</small>
            </label>
            <div class="col-2">
              <input class="form-check-input float-right w-auto" type="checkbox" name="richting" value="richting-1C" style="position:inherit;">
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Type -->
    <div class="margin-medium margin-border">
      <h2>Type</h2>
      <ul class="list-unstyled">
        <li class="row margin-small">
          <div class="col-8">
            Toelage<br/>
            <small>Lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor</small>
          </div>
          <div class="col-4">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-secondary active text-left">
                <input type="radio" name="toelage" value="toelage-ja" autocomplete="off">Ja
              </label>
              <label class="btn btn-secondary text-left">
                <input type="radio" name="toelage" value="toelage-nee" autocomplete="off">Nee
              </label>
            </div>
          </div>
        </li>
        <li class="row margin-small">
          <div class="col-8">
            Opleiding moeder<br/>
            <small>Lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor</small>
          </div>
          <div class="col-4">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-secondary active text-left">
                <input type="radio" name="opleidingMoeder" value="opleidingMoeder-ja" autocomplete="off">Ja
              </label>
              <label class="btn btn-secondary text-left">
                <input type="radio" name="opleidingMoeder" value="opleidingMoeder-nee" autocomplete="off">Nee
              </label>
            </div>
          </div>
        </li>
        <li class="row margin-small">
          <div class="col-8">
            Anderstalige leerling<br/>
            <small>Lorem ipsum dolor sit amet lorem ipsum dolor sit amet lorem ipsum dolor</small>
          </div>
          <div class="col-4">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-secondary active text-left">
                <input type="radio" name="anderstalig" value="anderstalig-ja" autocomplete="off">Ja
              </label>
              <label class="btn btn-secondary text-left">
                <input type="radio" name="anderstalig" value="anderstalig-nee" autocomplete="off">Nee
              </label>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Gemeente -->
    <div class="margin-medium">
      <h2>Gemeente</h2>
      <ul class="form-check list-unstyled">
        <li>
          <input class="form-check-input nestedCheckbox" type="checkbox" value="Sint-Truiden" id="Sint-Truiden" name="gemeente">
          <label class="form-check-label" for="option">
            Sint-Truiden
          </label>
          <ul class="list-unstyled">
            <li><label><input type="checkbox" value="Wellen" class="form-check-input nestedCheckboxSub" name="gemeente">Wellen</label></li>
            <li><label><input type="checkbox" value="Alken" class="form-check-input nestedCheckboxSub" name="gemeente">Alken</label></li>
            <li><label><input type="checkbox" value="Herk-De-Stad" class="form-check-input nestedCheckboxSub" name="gemeente">Herk-De-Stad</label></li>
          </ul>
        </li>
        <li>
          <input class="form-check-input nestedCheckbox" type="checkbox" value="Hasselt" id="Hasselt" name="gemeente">
          <label class="form-check-label" for="Hasselt">
            Hasselt
          </label>
          <ul class="list-unstyled">
            <li><label><input type="checkbox" value="Kermt" class="form-check-input nestedCheckboxSub" name="gemeente">Kermt</label></li>
            <li><label><input type="checkbox" value="Stevoort" class="form-check-input nestedCheckboxSub" name="gemeente">Stevoort</label></li>
            <li><label><input type="checkbox" value="Kiewit" class="form-check-input nestedCheckboxSub" name="gemeente">Kiewit</label></li>
          </ul>
        </li>
      </ul>
    </div>

    <!-- Submit button -->
    <button type="submit" class="btn btn-secondary btn-lg">Toon resultaten</button>

  </form>

</div>
