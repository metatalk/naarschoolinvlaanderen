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

  if(schooljaar == undefined || schooljaar == "") {
    schooljaar = new Date().getFullYear();
    $('[data-schooljaar="'+schooljaar+'"]').trigger('click');
    $('#schooljaarmodal').modal('show');
  }

  //var schooljaar = 2020;
  var schoolId = instelling;
  var schoolNaam = "";
  var url = appUrl+"/admin/databases/";

  var optieList = $('#optie-list');
  var optieWrapper = $('#optie-wrapper');
  var loaderStep1 = $('.loaderStep1');
  var loaderStep2 = $('.loaderStep2');
  var selectSchooljaar = $('.selectSchooljaar');
  var schoolTitel = $('#school-titel');
  var exportData = [];
  var exportBtn = $('#export');

  function initPage() {
    optieList.html('');
    $.ajax({ url: url+'opleidingen'+onderwijstype+'.data.json', cache: false, success: loadList });
  }

  initPage();

  function getSchoolNaam(data) {
    if(data[0].school) {
      schoolTitel.text(data[0].school);
      schoolNaam = data[0].school;
    }
  }

  function loadList(opleidingen) {
    var opleidingen = JSLINQ(opleidingen)
       .Where(function(item){ return item.vestiging_id == schoolId; })
       .orderBy(function(item) { return item.opleiding; });
    var opleidingen = opleidingen.items;
    if(opleidingen.length > 0) {
      generateTemplate(opleidingen);
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

        if(onderwijstype == 'secundair' && mergedData.administratieve_code != 6246 && mergedData.administratieve_code != 6247) {
          mergedData.disableInd = true;
        } else {
          mergedData.disableInd = false;
        }

        var html = theTemplate(mergedData);
        optieList.append(html);

        exportData.push(mergedData);
      }

      exportBtn.removeClass('disabled');

    }});

  }

  optieList.on('change', '.toggle-volzet', function(e){
     if(e.target.checked){
       var volzetCheckbox = $(this);
       var volzetModalId = volzetCheckbox.data('target');
       var volzetModal = $(volzetModalId);
       volzetModal.find('input').inputmask("99/99/9999 99:99",{
         "placeholder": "dd/mm/yyyy uu:mm",
         "oncomplete": function(){
           $('#registerVolzet'+volzetCheckbox.data('key')).removeAttr('disabled');
         }
       });
       volzetModal.modal().on('hidden.bs.modal', function (e) {
          if(volzetCheckbox.val().length < 1) {
            volzetCheckbox.prop("checked", false);
          }
        });
     } else {
       if(confirm("Ben je zeker dat je de volzetverklaring wil uitschakelen?") === true) {
         registerVolzet($(this),'');
       }
     }
  });

  optieList.on('click','.registerVolzet', function() {

      var volzetDatum = $('#volzetDatum'+$(this).data('key')).val();
      registerVolzet($(this),volzetDatum);

  });

  function registerVolzet(row,value) {

    var optionKey = row.data('key');
    var plaatsenRow = row.parents('.plaatsen-row');
    var logValue = value.length > 1 ? value : 'Niet volzet';

    $.ajax({
      type: "GET",
      url: "functions/savecijfer.php",
      data: {'field':'volzet','value':value,'opleiding_id':optionKey,'schooljaar':schooljaar,'type':onderwijstype},
      success: function(result) {
        if(result == 'ok') {
          logger({'data':'Volzet','waarde':logValue,'schoolid': schoolId, 'school':plaatsenRow.data('school') + ' - ' + plaatsenRow.data('opleiding')+ ' - ' + schooljaar},true);
        }
      }
    });

    return false;
  }

  optieList.on('change','[data-update]', function() {

    var plaatsenRow = $(this).parents('.plaatsen-row');
    var updateField = $(this).data('update');
    var optionKey = $(this).data('key');
    var optionValue = $(this).val();

    if(optionValue == "") {
      optionValue = 0;
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
        if(result == 'ok') {
          logger({'data':updateField,'waarde':optionValue,'schoolid': schoolId, 'school':plaatsenRow.data('school') + ' - ' + plaatsenRow.data('opleiding')+ ' - ' + schooljaar});
        }
      }
    });

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
    exportData = [];
    initPage();
  });

  // EXPORT TO CSV

  var headersSecundair = {
    administratieve_code: "Administratieve code",
    datum: "Laatst aangepast",
    graad: "Eerste graad",
    leerjaar: "Leerjaar",
    onderwijstype: "Onderwijstype",
    opleiding: "Opleiding",
    percentageind: "Percentage indicator",
    percentageindaantal: "Indicator aantal",
    plaatsen: "Capaciteit",
    plaatsenbezet: "Plaatsen bezet",
    plaatsenbezetind: "Plaatsen bezet indicator",
    volzet: "Volzet",
    vormtype: "Vormtype"
  }

  var headersLager = {
    datum: "Laatst aangepast",
    leerjaar: "Leerjaar",
    onderwijstype: "Onderwijstype",
    opleiding: "Opleiding",
    percentageind: "Percentage indicator",
    percentageindaantal: "Indicator aantal",
    plaatsen: "Capaciteit",
    plaatsenbezet: "Plaatsen bezet",
    plaatsenbezetind: "Plaatsen bezet indicator",
    volzet: "Volzet"
  }

  exportBtn.on('click', function() {
    var itemsFormatted = [];

    exportData.forEach((item) => {
      if(onderwijstype == 'secundair') {
        itemsFormatted.push({
          administratieve_code: item.administratieve_code.length > 0 ? item.administratieve_code.replace(/,/g, ' ') : ' ',
          datum: item.datum || 'Nog niet ingevuld',
          graad: item.graad || ' ',
          leerjaar: item.leerjaar.length > 0 ? item.leerjaar.replace(/,/g, ' ') : ' ',
          onderwijstype: item.onderwijstype || ' ',
          opleiding: item.opleiding.length > 0 ? item.opleiding.replace(/,/g, ' ') : ' ',
          percentageind: item.percentageind || ' ',
          percentageindaantal: item.percentageindaantal || ' ',
          plaatsen: item.plaatsen || ' ',
          plaatsenbezet: item.plaatsenbezet || ' ',
          plaatsenbezetind: item.plaatsenbezetind || ' ',
          volzet: item.volzet || ' ',
          vormtype: item.vormtype || ' '
        });
      } else {
        itemsFormatted.push({
          datum: item.datum || 'Nog niet ingevuld',
          leerjaar: item.leerjaar.length > 0 ? item.leerjaar.replace(/,/g, ' ') : ' ',
          onderwijstype: item.onderwijstype || ' ',
          opleiding: item.opleiding.length > 0 ? item.opleiding.replace(/,/g, ' ') : ' ',
          percentageind: item.percentageind || ' ',
          percentageindaantal: item.percentageindaantal || ' ',
          plaatsen: item.plaatsen || ' ',
          plaatsenbezet: item.plaatsenbezet || ' ',
          plaatsenbezetind: item.plaatsenbezetind || ' ',
          volzet: item.volzet || ' ',
        });
      }

    });

    var headers = onderwijstype == 'lager' ? headersLager : headersSecundair;

    exportCSVFile(headers,itemsFormatted,vestigingsnaam.replace(/,/g, ' ') + ' ' + schooljaar);

  });

  // FUNCTIONS

  function logger(data,reload = false) {
    console.log(data);
    $.ajax({ url: appUrl+'admin/functions/savelog.php', data: {'logdata':JSON.stringify(data)}, success: function() {
      if(reload === true) {
        location.reload();
      }
    }});
  }

  Handlebars.registerHelper('comma', function (text) {
      if(text.length > 0) {
        return text.replace(".",",");
      } else {
        return text;
      }
  });

  function convertToCSV(objArray) {
    var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
    var str = '';

    for (var i = 0; i < array.length; i++) {
        var line = '';
        for (var index in array[i]) {
            if (line != '') line += ','

            line += array[i][index];
        }

        str += line + '\r\n';
    }

    return str;
  }

  function exportCSVFile(headers, items, fileTitle) {
      if (headers) {
          items.unshift(headers);
      }

      // Convert Object to JSON
      var jsonObject = JSON.stringify(items);

      var csv = convertToCSV(jsonObject);

      var exportedFilenmae = fileTitle + '.csv' || 'export.csv';

      var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      if (navigator.msSaveBlob) { // IE 10+
          navigator.msSaveBlob(blob, exportedFilenmae);
      } else {
          var link = document.getElementById("export");
          if (link.download !== undefined) { // feature detection
              // Browsers that support HTML5 download attribute
              var url = URL.createObjectURL(blob);
              link.setAttribute("href", url);
              link.setAttribute("download", exportedFilenmae);
              link.click();
          }
      }
  }




});
