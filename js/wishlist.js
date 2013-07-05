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
  }
}

try {stmgr.loaded('wishlist.js');}catch(e){}