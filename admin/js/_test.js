var appUrl = 'https://naarschoolinvlaanderen.be/';

$(document).ready(function()  {

  if(instelling == undefined || instelling == "") {
    alert('Geen geldige instelling gevonden.');
    return false;
  }

  if(onderwijstype != "lager" && onderwijstype != "secundair") {
    alert('Geen geldige onderwijstype gevonden: '+onderwijstype);
    return false;
  }

  var schooljaar = 2019;
  var schoolId = instelling;
  var schoolNaam = "";
  var url = appUrl+"/admin/databases/";

  var optieList = $('#optie-list');
  var optieWrapper = $('#optie-wrapper');
  var loaderStep1 = $('.loaderStep1');
  var loaderStep2 = $('.loaderStep2');
  var selectSchooljaar = $('.selectSchooljaar');
  var schoolTitel = $('#school-titel');

  function initPage() {
    optieList.html('');
    loadList(opleidingen);
  }

  initPage();

  function getSchoolNaam(data) {
    if(data[0].school) {
      schoolTitel.text(data[0].school);
      schoolNaam = data[0].school;
    }
  }

  function loadList(opleidingen) {
    console.log(opleidingen);
    if(opleidingen.length > 0) {
      var opleidingen = JSLINQ(opleidingen).orderBy(function(item) { return item.sortgraad; });
      console.log(opleidingen.items);
      generateTemplate(opleidingen.items);
    } else {
      $('#optie-wrapper').hide();
      $('.bg-danger').removeClass('d-none');
      //alert('Geen vestiging gevonden met instelling ID ' + schoolId);
    }

  }

  function generateTemplate(opties) {

    var source   = $('#input-row')[0].innerHTML;
    var theTemplate = Handlebars.compile(source);
    var html    = theTemplate(opties);
    var graad = "";
    var test = null;

    $.ajax({ url: url+'entries'+schooljaar+onderwijstype+'.data.json', cache: false, success: function(data) {

      for(i = 0; i< opties.length; i++){

        test = JSLINQ(data).Where(function(item){ return item.opleiding_id == opties[i].id; });

        if(test.items.length == 1) {
          delete test.items[0].id;
          mergedData = Object.assign(opties[i], test.items[0]);
        } else {
          mergedData = opties[i];
        }

        if(mergedData.percentageind != "" && mergedData.percentageind > 0) {
          mergedData.percentageindaantal = Math.round((mergedData.plaatsen/100)*mergedData.percentageind);
        } else {
          mergedData.percentageindaantal = 0;
        }

        if(mergedData.plaatsen != "" && mergedData.plaatsenbezet > 0) {
          mergedData.vrijeplaatsen = mergedData.plaatsen - mergedData.percentageindaantal - mergedData.plaatsenbezet;
          mergedData.vrijeplaatsenind = mergedData.percentageindaantal - mergedData.plaatsenbezetind;
          mergedData.vrijeplaatsentotaal = mergedData.vrijeplaatsen + mergedData.vrijeplaatsenind;
        }

        mergedData.onderwijstype = onderwijstype;
        mergedData.school = schoolNaam;

        if(onderwijstype == 'Secundair' && mergedData.administratieve_code != 6246 && mergedData.administratieve_code != 6247) {
          mergedData.disableInd = true;
        } else {
          mergedData.disableInd = false;
        }

        var html = theTemplate(mergedData);
        optieList.append(html);
      }

    }});

  }

  optieList.on('change','[data-update]', function() {

    var plaatsenRow = $(this).parents('.plaatsen-row');
    var updateField = $(this).data('update');
    var optionKey = $(this).data('key');
    var optionValue = $(this).val();
    var abortVolzet = false;

    if(optionValue == "") {
      optionValue = 0;
    }

    if(updateField == "volzet") {
      if($(this).prop('checked')) {
        optionValue = 'ja';
      } else {
        optionValue = '';
      }
    }

    if(updateField == "percentageindtonen") {
      if($(this).prop('checked')) {
        optionValue = 'ja';
      } else {
        optionValue = '';
      }
    }

    if(updateField == "hide") {
      if($(this).prop('checked')) {
        optionValue = 'ja';
      } else {
        optionValue = '';
      }
    }

    if(updateField == "plaatsen" && !isNaN(optionValue) && optionValue > 0) {
      plaatsenRow.find('[data-update="percentageind"]').removeAttr('disabled');
    }

    if(abortVolzet === false) {

      var optionLoader = $(this).parent('.updatePlaatsen-loader').addClass('loading');
      var input = $(this).attr('disabled', 'disabled');

      $.ajax({
        type: "GET",
        url: "functions/savecijfer.php",
        data: {'field':updateField,'value':optionValue,'opleiding_id':optionKey,'schooljaar':schooljaar,'type':onderwijstype},
        success: function(result) {
          optionLoader.removeClass('loading');
          input.removeAttr('disabled');
          if(updateField != 'percentageindtonen') {
            input.addClass('form-control is-valid')
          }
          console.log(result);
          if(result == 'ok') {
            logger({'data':updateField,'waarde':optionValue,'school':plaatsenRow.data('school') + ' - ' + plaatsenRow.data('opleiding')+ ' - ' + schooljaar});
          }
        }
      });
    }
  });

  optieList.on('change','[data-update="percentageind"],[data-update="plaatsen"]', function() {
    var plaatsenRow = $(this).parents('.plaatsen-row');
    var updatePercentageIndicator = plaatsenRow.find('.updatePlaatsen-indicatorAantal');
    var plaatsenMessenenger = plaatsenRow.find('.plaatsen-messenger');
    var beschikbarePlaatsen = plaatsenRow.find('[data-update="plaatsen"]').val();
    var percentageValue = plaatsenRow.find('[data-update="percentageind"]').val();

    plaatsenRow.find('#percentageindtonen').show();

    if(isNaN(percentageValue)) {
      plaatsenMessenenger.text('Vul een nummer in.');
    } else {
      var totalIndPlaatsen = Math.round((beschikbarePlaatsen/100)*percentageValue);
      plaatsenRow.find('[data-update="plaatsen"]').data('indplaatsen',totalIndPlaatsen);
      updatePercentageIndicator.text('Waarvan '+totalIndPlaatsen+' indicator leerlingen.');
    }
  });

  optieList.on('change','[data-update="plaatsenbezet"],[data-update="plaatsenbezetind"],[data-update="plaatsen"],[data-update="percentageind"]', function() {
    var plaatsenRow = $(this).parents('.plaatsen-row');
    var beschikbarePlaatsen = plaatsenRow.find('[data-update="plaatsen"]').val();
    var beschikbarePlaatsenind = plaatsenRow.find('[data-update="plaatsen"]').data('indplaatsen');
    var vrijeplaatsen = beschikbarePlaatsen - beschikbarePlaatsenind - plaatsenRow.find('[data-update="plaatsenbezet"]').val();
    var vrijeplaatsenInd = beschikbarePlaatsenind - plaatsenRow.find('[data-update="plaatsenbezetind"]').val();

    vrijeplaatsen = parseInt(vrijeplaatsen) + parseInt(vrijeplaatsenInd);

    if(beschikbarePlaatsen > 0) {
      plaatsenRow.find('.updatePlaatsen-vrijeplaatsen').html('In totaal nog '+vrijeplaatsen+' vrije plaatsen.');
    } else {
      plaatsenRow.find('.updatePlaatsen-vrijeplaatsen').html('');
    }
  });

  selectSchooljaar.on('click','a', function() {
    var tabs = $(this).parents('.selectSchooljaar');
    tabs.find('a').removeClass('active');
    $(this).addClass('active');
    schooljaar = $(this).data('schooljaar');
    initPage();
  });

  function logger(data) {
    $.ajax({ url: appUrl+'admin/functions/savelog.php', data: data });
  }

  Handlebars.registerHelper('comma', function (text) {
      if(text.length > 0) {
        return text.replace(".",",");
      } else {
        return text;
      }
  });

});
