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
  },

  subscribe: function(id) {
    ajax.post('/market?act=subscribe&id='+ id, {}, function(r) {
      $('#subscribe'+ id).html('<span class="icon-white icon-check"></span> '+ ((r.step == 0) ? 'Отписаться от новостей' : 'Подписаться на новости'));
    }, function(r) {

    });
  },

  shareToFriends: function(id) {
    showGlobalPrg();

    ajax.post('/market?act=sharetofriends&id='+ id, {}, function(r) {
      hideGlobalPrg();

      var box = new Box({
        hideButtons: true,
        bodyStyle: 'padding: 0px',
        width: 500
      });
      box.content(r.html);
      box.show();
    });
  },

  doShareToFriends: function(id) {
    $('div.box_progress').show();

    ajax.post('/market?act=sharetofriends&id='+ id, {msg: $('#share_message').val()}, function(r) {
      $('div.box_progress').hide();

      if (r.success) {
        curBox().hide();
        boxPopup('Сообщение успешно отправлено');
      }
      else boxPopup('Произошла ошибка. Повторите запрос позже');
    }, function(r) {
      $('div.box_progress').hide();
    });
  }
}

try {stmgr.loaded('market.js');}catch(e){}