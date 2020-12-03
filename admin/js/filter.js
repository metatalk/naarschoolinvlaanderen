$('document').ready(function() {
  var filterInput = $('[data-filter]');

  filterInput.on('keyup', function() {
    filterWrapper = $($(this).data('filter'));
    filterObjects = filterWrapper.find('[data-filtertext]');
    if($(this).val().length > 0) {
      filterObjects.hide();
      filterWrapper.find('[data-filtertext*="'+$(this).val().toLowerCase()+'"]').show();
    } else {
      filterObjects.show();
    }
  });
});
