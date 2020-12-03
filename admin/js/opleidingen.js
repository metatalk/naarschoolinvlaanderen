var appUrl = 'https://naarschoolinvlaanderen.be';
var url = appUrl+"/admin/";

$(document).ready(function()  {

  $('[data-toggle="tooltip"]').tooltip();

  var opleidingenWrapper = $('#opleidingen-wrapper');

  saveButtonClicked = false;

  $('#sync-onderwijskiezer:not([disabled])').one('click', function(e){
    e.preventDefault();
    var vestigingstype = $(this).data('type');
    var vestiginsnummer = $(this).data('id');
    $(this).attr('disabled',true);
    $(this).find('.spinner-border').removeClass('d-none');

      $.ajax({
        url: url+'functions/loadonderwijskiezer.php',
        data: {
          'type' : vestigingstype,
          'id': vestiginsnummer
        },
        contentType: 'json',
        type: 'GET'
      }).done(function(data){
        generateRows(JSON.parse(data));
      }).fail(function(){
        console.log("An error occurred, the data couldn't be sent!");
      });

  });

  $('#nieuwe-opleiding').on('click', function() {
    createRow(false);
  });

  $('#opslaan').on('click', function(e) {
    e.preventDefault();
    var formData = new FormData($('#opleidingen-form')[0]);
    $.ajax({
      url: url + 'functions/saveopleiding.php',
      data: formData,
      type: 'POST',
      processData: false,
      contentType:false
    }).done(function(data){
      saveButtonClicked = true;
      location.reload();
    }).fail(function(){
      console.log("An error occurred, the data couldn't be sent!");
    });
  });

  function generateRows(data) {
    var onderwijskiezerData = data;
    if(onderwijskiezerData.length <= 0) {
      alert('Geen resultaten gevonden in onderwijskiezer voor vestiging met nummer ' + $('#sync-onderwijskiezer').data('id'));
    } else {
      var count = onderwijskiezerData.length;
      $.each(onderwijskiezerData, function(index,value) {
        createRow(value,index);
        if (!--count) {
          $('.nieuweopleiding[data-index="1"]:first').remove();
        }
      });
      $('#onderwijskiezer-alert').slideUp(400);
      $('#sync-onderwijskiezer').removeAttr('disabled').find('.spinner-border').addClass('d-none');
    }
  }


  generateNewRow = 0;

  function createRow(data,index) {

    var lastRow = opleidingenWrapper.find('.nieuweopleiding').last();

    if(generateNewRow === 0 && data === false) {
      lastRow.removeClass('d-none');
    } else {

      var newRow = lastRow.clone();
      var controlNumber = Math.round(Math.random() * 100000);
      var newrowIndex = index ? index + controlNumber :lastRow.data('index') + controlNumber;
      newRow.data('index',newrowIndex);

      newRow.attr('data-index',newrowIndex);

      newRow.find('input,select').each(function(index,element) {
        switch($(element).data('field')) {
          case 'opleiding':
            var opleidingNaam = data !== false ? cleanName(data.korte_naam) : '';
            $(element).val(opleidingNaam);
          break;
          case 'vormtype':
            var onderwijsVorm = data !== false ? data.ko_onderwijsvorm_s : '';
            $(element).val(onderwijsVorm);
          break;
          case 'leerjaar':
            $(element).find('option:selected').removeAttr('selected');
            if(data !== false) {
              $(element).find('option:eq('+data.leerjaar+')').attr('selected',true);
            }
          break;
          case 'graad':
            $(element).find('option:selected').removeAttr('selected');
            if(data !== false && data.graad_eht < 4) {
              $(element).find('option:eq('+data.graad_eht+')').attr('selected',true);
            }
          break;
        }
        $(element).attr('name','new['+newrowIndex+']['+$(element).data('field')+']')
      });
      opleidingenWrapper.append(newRow);
    }

    generateNewRow++;
  }

  function cleanName(opleiding) {
    var cleanOpleiding = opleiding.replace('1e lj ','').replace('2e lj ','');
    cleanOpleiding = cleanOpleiding.replace('1e gr ','').replace('2e gr ','').replace('3e gr ','');
    return cleanOpleiding;
  }

  window.addEventListener("beforeunload", function (e) {

    //var isButton = e.activeElement.type && e.activeElement.type == "type" ? true : false;

    if(generateNewRow > 0 && saveButtonClicked === false) {
      console.log(e);
      var confirmationMessage = "Ben je zeker dat je wil sluiten? Je hebt mogelijk nog niet alle wijzigingen opgeslaan.";

      (e || window.event).returnValue = confirmationMessage;
      return confirmationMessage;
    }
  });

  $(document).on('click','a.removenewrow', function(e) {
    e.preventDefault();
    if(generateNewRow === 1) {
      $(this).parents('.nieuweopleiding').addClass('d-none');
      generateNewRow = 0;
    } else {
      $(this).parents('.nieuweopleiding').remove();
      generateNewRow--;
    }
  });

});
