var Wishlist = {
  add: function(type) {
    showGlobalPrg();

    ajax.post('/wishlist?act=add&type='+ type, {}, function(r) {
      hideGlobalPrg();

      var box = new Box({
        hideButtons: true,
        bodyStyle: 'padding: 0px'
      });
      box.content(r.html);
      box.show();

      $('#content').trigger('contentChanged');
    }, function(x) {
      hideGlobalPrg();
    });
  },
  attemptAdd: function() {
    var shortstory = $.trim($('#shortstory').val()), type = $('#type').val();

    if (!shortstory) {
      $('#shortstory').effect('highlight');
      return false;
    }

    var params = {
      type: type,
      shortstory: shortstory
    }

    $('#box_progress').show();
    ajax.post('/wishlist?act=add&type='+ type, params, function(r) {
      $('#box_progress').hide();

      if (r.success) {
        $('#wishlist_error').hide();
        boxPopup(r.message);
        curBox().hide();
      } else {
        $('#wishlist_error').html(r.errors).show();
      }
    }, function(x) {
      $('#box_progress').hide();
    });
  },
  edit: function(id) {
    showGlobalPrg();

    ajax.post('/wishlist?act=edit&id='+ id, {}, function(r) {
      hideGlobalPrg();

      var box = new Box({
        hideButtons: true,
        bodyStyle: 'padding: 0px'
      });
      box.content(r.html);
      box.show();

      $('#content').trigger('contentChanged');
    }, function(x) {
      hideGlobalPrg();
    });
  },
  attemptEdit: function() {
    var shortstory = $.trim($('#shortstory').val()), id = $('#wishlist_id').val();

    if (!shortstory) {
      $('#shortstory').effect('highlight');
      return false;
    }

    var params = {
      id: id,
      shortstory: shortstory
    }

    $('#box_progress').show();
    ajax.post('/wishlist?act=edit&id='+ id, params, function(r) {
      $('#box_progress').hide();

      if (r.success) {
        $('#wishlist_error').hide();
        boxPopup(r.message);
        curBox().hide();
      } else {
        $('#wishlist_error').html(r.errors).show();
      }
    }, function(x) {
      $('#box_progress').hide();
    });
  },
  delete: function(id) {
    showConfirmBox('Удаление пожелания', 'Вы действительно хотите удалить пожелание?', 'Удалить', function() {
      curBox().showProgress();
      ajax.post('/wishlist?act=delete&id='+ id, {}, function(r) {
        curBox().hide();
        boxPopup(r.message);
      }, function(r) {
        curBox().hideProgress();
      });
    }, 'Отмена', function() {
      curBox().hide();
    });
  },
  showMore: function(el, limit, type, act) {
    if (!cur['wishesOffset']) cur.wishesOffset = {};
    if (!cur.wishesOffset[type]) cur.wishesOffset[type] = limit;
    else cur.wishesOffset[type] += limit;

    if (!act) act = 'index';

    $(el).html('<img src="/images/upload.gif" />');
    ajax.post('/wishlist?act='+ act +'&offset='+ cur.wishesOffset[type], {type: type, pages: 1}, function(r) {
      $(r.html).appendTo('#wishlist_'+ ((type == 1) ? 'wants' : 'cans'));
      if (r.html == '') {
        $(el).hide();
      }
      $(el).html('Показать еще');
    }, function(r) {
      $(el).html('Показать еще');
    });
  }
}

try {stmgr.loaded('wishlist.js');}catch(e){}