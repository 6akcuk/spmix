var Wall = {
  showEditor: function() {
    if (A.wallPostOpened) return;
    if (!A.wallPostOpened) A.wallPostOpened = true;
    if (typeof A.wallPhotoAttaches == 'undefined') A.wallPhotoAttaches = 0;

    $('#wall_post').css({minHeight: 32, height: 32});
    $('#wall_post_btn').show();

    setTimeout(function() {
      $('body').one('click', function() {
        $('#wall_post').css({minHeight: 14, height: 14});
        $('#wall_post_btn').hide();
        A.wallPostOpened = false;
      });
    }, 100);
  },

  attachPhoto: function(file_id) {
    if (A.wallPhotoAttaches >= 3) {
      boxPopup('Вы не можете прикрепить более 3-х фотографий');
      return;
    }

    cur['fileDoneCustom'+ file_id] = Wall.onUploadDone.pbind(file_id);
    Upload.onStart(file_id);
  },

  onUploadDone: function(file_id, filedata) {
    var json = $.parseJSON(filedata);
    Upload.showInput(file_id);
    Upload.initFile(file_id, function() {
      Wall.attachPhoto(file_id);
    });

    $('<input/>').attr({type: 'hidden', id: 'wall_'+ file_id +'_attach', name: 'Wall[attach][photo][]'}).val(filedata).prependTo('#wall_post_btn');
    var cont = $('<div/>').attr({class: 'left wall_attach_photo'}).appendTo('#wall_attaches');
    cont.html('<img src="http://cs'+ json['b'][2] +'.'+ A.host +'/'+ json['b'][0] +'/'+ json['b'][1] +'" alt=""/><a class="tt photo_attach_delete" title="Удалить фотографию"><span class="icon-remove icon-white"></span></a>');
    A.wallPhotoAttaches++;

    if (A.wallPhotoAttaches >= 3) {
      $('#file_button_'+ file_id).hide();
    }

    cont.children('a').click(function() {
      $('#file_button_'+ file_id).show();
      A.wallPhotoAttaches--;
      if (A.wallPhotoAttaches < 0) A.wallPhotoAttaches = 0;
      cont.remove();
      $('#wall_'+ file_id +'_attach').remove();
    });
  },

  doPost: function(wall_id) {
    var post = $.trim($('#wall_post').val()), wall = {post: null, attach: {photo: []}};
    if (!post) {
      $('#wall_post').focus();
      return;
    }

    wall.post = post;
    $('#wall_post_btn input[name*="Wall[attach][photo]"]').each(function(i, item) {
      wall.attach.photo.push($(item).val());
    });

    if (A.wallPostSending) return;
    A.wallPostSending = true;

    $('#wall_post_progress').show();
    ajax.post('/users/profiles/wallpost?id='+ wall_id, {last_id: A.wallLastID, wall: wall}, function(r) {
      A.wallPostSending = false;
      $('#wall_post_progress').hide();

      $('#wall_post').val('').blur();
      $('body').click();

      $('#wall_header').html(r.num);
      $(r.posts).prependTo('#wall'+ wall_id);
      A.wallPhotoAttaches = 0;
      $('#wall_attaches').html('');
      $('#wall_post_btn input[name*="Wall[attach][photo]"]').remove();
    }, function(xhr) {
      A.wallPostSending = false;
      $('#wall_post_progress').hide();
    });
  },

  postOver: function(id, event) {
    if (event.target.id == 'delete_post'+ id) return;
    $('#delete_post'+ id).css({opacity: 0.3})
  },
  postOut: function(id, event) {
    $('#delete_post'+ id).css({opacity: 0});
  },
  postDeleteOver: function(id) {
    $('#delete_post'+ id).css({opacity: 1});
    Tooltip.show('#delete_post'+ id);
  },
  postDeleteOut: function(id) {
    $('#delete_post'+ id).css({opacity: 0.3});
    Tooltip.hide();
  },
  deletePost: function(id) {
    var id = id.split('_'), post_id = id.pop(), wall_id = id.pop();
    ajax.post('/users/profiles/deletewallpost?id='+ post_id, {}, function(r) {
      $('#wall'+ wall_id + '_'+ post_id + ' div.post_table').hide();
      $('<div id="post_del'+ wall_id + '_'+ post_id +'" class="dld">Сообщение удалено. <a onclick="Wall.restorePost(\''+ wall_id +'_'+ post_id +'\')">Восстановить</a></div>').insertAfter('#wall'+ wall_id +'_'+ post_id + ' div.post_table');
    }, function(xhr) {

    });
  },
  restorePost: function(id) {
    var id = id.split('_'), post_id = id.pop(), wall_id = id.pop();
    ajax.post('/users/profiles/restorewallpost?id='+ post_id, {}, function(r) {
      $('#wall'+ wall_id + '_'+ post_id + ' div.post_table').show().next().remove();
    }, function(xhr) {

    });
  }
}

try {stmgr.loaded('wall.js');}catch(e){}