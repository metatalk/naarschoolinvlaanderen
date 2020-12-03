<script id="scholen-template" type="text/x-handlebars-template">
  <div class="shadow rounded bg-white padding margin-small w-100 scholen-item">
    <div class="row">
      <h1>{{id}}</h1>
      <div class="col-sm-2 mobile-margin-bottom">
        <div class="scholen-item-img" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url('https://bit.ly/2SEOJVV');"></div>
      </div>
      <div class="col-sm-6 mobile-margin-bottom">
        <small>Onderwijsnet: {{Onderwijsnet}}</small>
        <h2 class="margin-mini">{{School}}</h2>
        <ul class="list-unstyled no-margin">
          <li><i class="fas fa-phone"></i> <a href="tel:' field.telefoonnummer '">{{telefoonnummer}}</a></li>
          <li><i class="fas fa-map-marker-alt"></i> <a href="https://maps.google.com" target="_blank">{{Adres}}, {{Postcode}} {{Gemeente}}</a></li>
        </ul>
      </div>
      <div class="col-sm-4">
        <div class="margin-small clearfix">
          <span class="scholen-item-badge badge badge-success">Nog {{Plaatsen}} plaatsen</span>
        </div>
        <a class="d-block" href="field.link" target="_blank">Meer info en inschrijven</i></a>
        <small class="text-muted">Laatst updated: {{Update}}</small>
      </div>
    </div>
  </div>
</script>
