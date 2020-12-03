$(document).ready(function() {

  //* START *//

  $('[data-toggle="popover"]').popover('show');

  $.ajaxSetup({ cache: false });

  var onderwijsType = "lager";

  var scholenDataUrl = appUrl+'admin/databases/';
  var gemeenteSelect = $('[data-filter="gemeente"]');
  var opleidingenSelect = $('[data-filter="opleiding"]');
  var leerjarenSelect = $('[data-filter="leerjaar"]');
  var schooljaarSelect = $('input[name="schooljaar"]');
  var resultWrapper = $('#app-results');

  var filter = {
    schooljaar: schooljaarSelect.val(),
    gemeente: [],
    opleiding: '',
    leerjaar: '',
    indicator: false
  };

  var dataUrl = appUrl+'admin/functions/loadpublic.php'

  var plaatsen = [];

  // MISC
  var titleOpleiding = $('#title-opleiding');
  var titleGemeente = $('#title-gemeente');
  var titleCount = $('.title-count');

  // url
  var urlGemeente = decodeURIComponent(GetURLParameter('gemeente'));
  var urlType = GetURLParameter('type');
  var urlOpleiding = decodeURIComponent(GetURLParameter('opleiding'));

  // ui mobile
  var mobileTriggerFilter = $('.toggle-filter');
  var appFilter = $('.app-filter');

  mobileTriggerFilter.on('click', function(e) {
    e.preventDefault();
    appFilter.toggleClass('active');
  });

  gemeenteSelect.multiselect({
    placeholder: 'Selecteer gemeentes',
    maxWidth: '100%',
    maxHeight: '235px',
    maxPlaceholderOpts: 0,
    search   : true,
    selectGroup: true,
    texts    : {
      placeholder: 'Selecteer gemeentes',
      search     : 'Typ hier je gemeente om te zoeken'
    },
    onLoad: function() {
      var closeBtn = $('<a class="closegemeentelist bg-success text-white">Verder filteren</a>');
      $(document)
        .find('.ms-options-wrap')
        .append(closeBtn);
      closeBtn.on('click', function() {
        $(document).trigger('click.ms-hideopts');
        $(this).hide();
      });
    },
    onOptionClick: function() {
      $('.ms-options-wrap').find('.closegemeentelist').show();
    },
    onControlClose: function() {
      $('.ms-options-wrap').find('.closegemeentelist').hide();
    }
  });

  if(urlType === 'lager' || urlType === 'secundair') {
    loadType(urlType);
    $('input[name="typeonderwijs"][value="'+urlType+'"]').trigger('click');
  }


  $('input[name="typeonderwijs"]').on('change', function() {
    setUrlParameter('type',$(this).val());
    setUrlParameter('gemeente','');
    setUrlParameter('opleiding','');
    location.reload();
    //loadType($(this).val());
    //appStartscreen(true);
  });

  if(urlGemeente.length > 1 && urlGemeente != 'undefined') {
    filter.gemeente = urlGemeente;
    loadResults();
  }

  function loadType(type) {
    if(type) {
      onderwijsType = type;
    }
    $.ajax({
      url: dataUrl,
      data: {function:'loadgemeentenlijst',type:onderwijsType, gemeenten:urlGemeente},
      dataType: 'json',
      success: loadList
    });
    mobileTriggerFilter.removeAttr('disabled');
  }


  function loadList(data) {
    gemeenteSelect.multiselect('loadOptions',data);
    $('#filter-gemeente, #filter-schooljaar').removeClass('app-filtergroup-disabled').find('input,select').removeAttr('disabled');
    $('[data-toggle="popover"]').popover('dispose');
    $('select[multiple]').multiselect('reload');
    updateGemeenteTitle();
  }

  gemeenteSelect.on('change', function() {
    filter.gemeente = $(this).val().join(",");
    setUrlParameter('gemeente',$(this).val().join(","));
    updateGemeenteTitle();
    setTimeout(function(){
      loadResults();
    }, 800);
  });

  opleidingenSelect.on('change', function() {
    filter.opleiding = $(this).val();
    if(onderwijsType == "lager") {
      leerjarenSelect.find('option').removeAttr('selected');
      leerjarenSelect.find('option:first').attr('selected',true);
    }
    filterSecondary();
  });

  leerjarenSelect.on('change', function() {
    filter.leerjaar = $(this).val();
    if(onderwijsType == "secundair") {
      filter.opleiding = "";
      opleidingenSelect.find('option').removeAttr('selected');
      opleidingenSelect.find('option:first').attr('selected',true);
    }
    filterSecondary();
  });

  schooljaarSelect.on('change', function() {
    filter.schooljaar = $(this).val();
    console.log(filter.schooljaar);
    if(filter.gemeente.length > 0) {
      loadResults();
    }
  });

  function loadResults() {

      $.ajax({
        url: dataUrl,
        data: {function:'loadgemeentes',type:onderwijsType, year:filter.schooljaar, gemeenten: filter.gemeente},
        dataType: 'json',
        beforeSend: function(){
          $('#loader').addClass('loading');
        },
        success: function(data) {
          if(filter.gemeente.length > 0) {
            generateTemplate(data);
          } else {
            resultWrapper.html('');
          }

          generateOpleidingList(data);
          generateLeerjaarList(data);
          appStartscreen(false);
          enableFilters();
          filterSecondary();
          $('#loader').removeClass('loading');
        }
      });

  }


  function generateTemplate(data) {

    var source   = $('#input-school')[0].innerHTML;
    var theTemplate = Handlebars.compile(source);
    var html    = '';
    resultWrapper.html('');

    for(i=0; i < data.length; i++) {
      if(data[i].opleidingen.length > 0) {
        html = theTemplate(data[i]);
        resultWrapper.append(html);
      }
    }

    $('[data-toggle="tooltip"]').tooltip()

  }

  function generateOpleidingList(data) {
    var opleidingenList = [];
    for(i=0; i < data.length; i++) {
      for(r = 0; r < data[i].opleidingen.length; r++){

        if(onderwijsType == "lager" && opleidingenList.filter(function(e) { return e.opleiding === data[i].opleidingen[r].opleiding; }).length <= 0 && data[i].opleidingen[r].opleiding !== '') {
          opleidingenList.push({'administratieve_code': data[i].opleidingen[r].opleiding, 'opleiding': data[i].opleidingen[r].opleiding});
        } else if(onderwijsType == "secundair") {

          var opleidingExists = opleidingenList.filter(function(e) {
            return e.opleiding === data[i].opleidingen[r].opleiding;
          });

          if(data[i].opleidingen[r].opleiding !== '' && data[i].opleidingen[r].administratieve_code != '') {
            opleidingenList.push({'administratieve_code': data[i].opleidingen[r].administratieve_code, 'opleiding': data[i].opleidingen[r].opleiding});
          }
        }

          /*if(opleidingenList.filter(function(e) { return e.opleiding === data[i].opleidingen[r].opleiding; }).length <= 0 && data[i].opleidingen[r].opleiding !== ''){
            if(data[i].opleidingen[r].administratieve_code && data[i].opleidingen[r].administratieve_code.length > 1) {
              opleidingValue = data[i].opleidingen[r].administratieve_code;
            } else {
              opleidingValue = data[i].opleidingen[r].opleiding;
            }

          }*/
      }
    }
    opleidingenList.sort();
    opleidingenSelect.html('<option value=""> Filter op opleiding</option>');
    $.each(opleidingenList,function(i,value) {
      if(filter.opleiding && filter.opleiding === value.administratieve_code) {
        selected = 'selected';
      } else {
        selected = '';
      }
      opleidingenSelect.append('<option value="'+value.administratieve_code+'" '+selected+'>'+value.opleiding+'</option>');
    });
    sortOptions(opleidingenSelect);
  }

  function generateLeerjaarList(data) {
    var leerjarenList = [];
    for(i=0; i < data.length; i++) {
      for(r = 0; r < data[i].opleidingen.length; r++){
          if(leerjarenList.filter(function(e) { return e.leerjaar.trim() === data[i].opleidingen[r].leerjaar.trim(); }).length <= 0 && data[i].opleidingen[r].leerjaar !== ''){
              leerjarenList.push({'leerjaar': data[i].opleidingen[r].leerjaar.trim(),'opleiding': data[i].opleidingen[r].opleiding});
          }
      }
    }
    var labelText = onderwijsType == "lager" ? " Filter op leerjaar of geboortejaar" : " Filter op leerjaar";
    leerjarenSelect.html('<option value="" data-opleidingnaam="">'+labelText+'</option>');
    $.each(leerjarenList,function(i,value) {
      if(filter.leerjaar.length > 0 && filter.leerjaar === value.leerjaar) {
        selected = 'selected';
      } else {
        selected = '';
      }
      leerjarenSelect.append('<option data-opleidingnaam="'+value.opleiding+'" value="'+value.leerjaar+'" '+selected+'>'+value.leerjaar+'</option>');
    });

  }

  function filterSecondary() {

    var allResults = resultWrapper.find('tr[data-opleiding]').show();
    var allCards = resultWrapper.find('.card').show();
    var noResults = $('#noresults').hide();
    var filterOpleiding = false;
    var filterString = '';

    if(onderwijsType == "secundair") {
      sortOptions(leerjarenSelect);
      if(filter.leerjaar.length < 1) {
        $('#filter-opleiding').hide();
      }
    }

    if(filter.leerjaar.length > 1 || filter.opleiding.length > 1) {

      allResults.hide();

      if(filter.leerjaar.length > 1) {
        filterString += '[data-leerjaar="'+filter.leerjaar+'"]';
        $('#filter-opleiding').show();
      }

      if(filter.opleiding.length > 1) {
        filterString += '[data-opleiding="'+filter.opleiding+'"]';
        if(onderwijsType == "lager") {
          allResults.filter('[data-leerjaar=""][data-opleiding="'+filter.opleiding+'"]').show();
          leerjarenSelect.find('option').hide().filter('[data-opleidingnaam="'+filter.opleiding+'"],[data-opleidingnaam=""]').show();
        }
      }

      filterOpleiding = allResults.filter(filterString).show();

      if(filter.leerjaar.length > 1 && filter.opleiding.length < 1) {

        opleidingenSelect.find('option').hide();

        filterOpleiding.each(function() {
          var containsOpleiding = $(this).data('opleiding');
          opleidingenSelect.find('[value="'+containsOpleiding+'"],[value=""]').show();
        });
      }

      allCards.each(function(index) {
        if($(this).find('tr[data-opleiding]:visible').length == 0) {
          $(this).hide();
        }
      });

      if(allCards.filter(':visible').length == 0) {
        noResults.show();
      }

    }


  }

  function appLoader() {

  }

  function sortOptions(selectItem) {
    selectItem = selectItem.find('option');
    var arr = selectItem.map(function(_, o) {
        return {
            t: $(o).text(),
            v: o.value
        };
    }).get();
    arr.sort(function(o1, o2) {
        return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0;
    });
    selectItem.each(function(i, o) {
        o.value = arr[i].v;
        $(o).text(arr[i].t);
    });
  }

  function appStartscreen(state) {
    $('.app-startscreen').toggleClass('app-startscreen-show',state);
  }

  function enableFilters() {
    if(filter.gemeente.length > 0) {
      $('#filter-opleiding,#filter-schooljaar').removeClass('app-filtergroup-disabled').find('input,select').removeAttr('disabled');
    } else {
      $('#filter-opleiding,#filter-schooljaar').addClass('app-filtergroup-disabled').find('input,select').attr('disabled');
      appStartscreen(true);
    }
    if(filter.gemeente.length > 0) {
      $('#filter-leerjaar').removeClass('app-filtergroup-disabled').find('input,select').removeAttr('disabled');
    } else {
      $('#filter-leerjaar').addClass('app-filtergroup-disabled').find('input,select').attr('disabled');
    }
  }

  function updateGemeenteTitle() {
    if(filter.gemeente != "") {
      $('#show-title-gemeente').show();
      titleGemeente.text(filter.gemeente);
    } else {
      $('#show-title-gemeente').hide();
    }
  }

});

Handlebars.registerHelper('trimString', function(passedString) {
    var theString = passedString.trim();
    return new Handlebars.SafeString(theString)
});

// For todays date;
Date.prototype.today = function () {
    return ((this.getDate() < 10)?"0":"") + this.getDate() +"-"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"-"+ this.getFullYear();
}

// For the time now
Date.prototype.timeNow = function () {
     return ((this.getHours() < 10)?"0":"") + this.getHours() +"-"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +"-"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
}

function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam){
            return sParameterName[1];
        }
    }
}

function setUrlParameter(key, value) {

    var url = window.location.href;

    var key = encodeURIComponent(key),
        value = encodeURIComponent(value);

    var baseUrl = url.split('?')[0],
        newParam = key + '=' + value,
        params = '?' + newParam;

    if (url.split('?')[1] == undefined){ // if there are no query strings, make urlQueryString empty
        urlQueryString = '';
    } else {
        urlQueryString = '?' + url.split('?')[1];
    }

    // If the "search" string exists, then build params from it
    if (urlQueryString) {
        var updateRegex = new RegExp('([\?&])' + key + '[^&]*');
        var removeRegex = new RegExp('([\?&])' + key + '=[^&;]+[&;]?');

        if (typeof value === 'undefined' || value === null || value === "") {
            params = urlQueryString.replace(removeRegex, "$1");
            params = params.replace(/[&;]$/, "");

        } else if (urlQueryString.match(updateRegex) !== null) {
            params = urlQueryString.replace(updateRegex, "$1" + newParam);

        } else if (urlQueryString=="") {
            params = '?' + newParam;
        } else {
            params = urlQueryString + '&' + newParam;
        }
    }

    // no parameter was set so we don't need the question mark
    params = params === '?' ? '' : params;

    window.history.pushState(null, null, baseUrl + params);
}
