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

  edit: function() {
    if ($('#market_add').parent().hasClass('button_disabled')) return false;

    $('#market_add').parent().addClass('button_disabled');
    $('#market_add_progress').show();
    FormMgr.submit('#market_add_form', 'top', function(r) {
      $('#market_add').parent().removeClass('button_disabled');
      $('#market_add_progress').hide();

      if (r.success) {
        boxPopup(r.msg);
        nav.reload();
      }
    }, function(r) {
      $('#market_add').parent().removeClass('button_disabled');
      $('#market_add_progress').hide();
    });

    return false;
  },

  deleteGood: function(id) {
    showConfirmBox('Удаление товара', 'Вы действительно хотите удалить товар?', 'Удалить', function() {
      curBox().showProgress();
      ajax.post('/market?act=delete&id='+ id, {}, function(r) {
        curBox().hideProgress();
        boxPopup(r.message);
      }, function(r) {
        curBox().hideProgress();
      });
    }, 'Отмена', function() {
      curBox().hide();
    });
  },

  buy: function(id, msg) {
    showGlobalPrg();

    ajax.post('/write'+ id, {msg: msg, box_request: 1}, function(r) {
      hideGlobalPrg();

      var box = new Box({
        hideButtons: true,
        bodyStyle: 'padding:0px;border:0px',
        width: 502
      });
      box.content(r.html);
      box.show();

      $('#content').trigger('contentChanged');
    }, function(r) {
      hideGlobalPrg();
    });
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