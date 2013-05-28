var Market = {
  add: function() {
    if ($('#market_add').parent().hasClass('button_disabled')) return false;

    $('#market_add').parent().addClass('button_disabled');
    $('#market_add_progress').show();
    FormMgr.submit('#market_add_form', 'top', function(r) {
      $('#market_add').parent().removeClass('button_disabled');
      $('#market_add_progress').hide();

      if (r.success) {
        boxPopup(r.msg);
        $('#market_add_form input, #market_add_form textarea').val('');
      }
    }, function(r) {
      $('#market_add').parent().removeClass('button_disabled');
      $('#market_add_progress').hide();
    });

    return false;
  }
}

try {stmgr.loaded('market.js');}catch(e){}