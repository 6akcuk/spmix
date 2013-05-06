var Discuss = {
  createForum: function() {
    var title = $.trim($('#bnt_title').val()),
      description = $.trim($('#bnt_description').val()),
      parent_id = $.trim($('#bnt_parent').val()),
      icon = $.trim($('#icon').val()),
      city = parseInt($('#bnt_city').val()),
      rights = parseInt($('#bnt_rights').val());

    if (!title) {
      $('#bnt_title').focus();
      return false;
    }

    if (A.discussForumCreate) return;
    A.discussForumCreate = true;

    $('#bnt_progress').show();
    ajax.post('/discuss?act=create', {title: title, description: description, parent_id: parent_id, icon: icon, city: city, rights: rights}, function(r) {
      $('#bnt_progress').hide();
      A.discussForumCreate = false;

      boxPopup(r.msg);
      nav.go(r.url);
    }, function(xhr) {
      $('#bnt_progress').hide();
      A.discussForumCreate = false;
    });
  },

  doEditForum: function(id) {
    var title = $.trim($('#bnt_title').val()),
      description = $.trim($('#bnt_description').val()),
      parent_id = $.trim($('#bnt_parent').val()),
      icon = $.trim($('#icon').val()),
      city = parseInt($('#bnt_city').val()),
      rights = parseInt($('#bnt_rights').val());

    if (!title) {
      $('#bnt_title').focus();
      return false;
    }

    if (A.discussForumEdit) return;
    A.discussForumEdit = true;

    $('#bnt_progress').show();
    ajax.post('/discuss?act=edit&id='+ id, {title: title, description: description, parent_id: parent_id, icon: icon, city: city, rights: rights}, function(r) {
      $('#bnt_progress').hide();
      A.discussForumEdit = false;

      boxPopup(r.msg);
      nav.go(r.url);
    }, function(xhr) {
      $('#bnt_progress').hide();
      A.discussForumEdit = false;
    });
  },

  deleteForum: function(id) {
    showConfirmBox('Вы уверене, что хотите удалить данный форум? Данное действие удалит все темы и сообщения, его нельзя будет отменить', 'Да', function() {
      Discuss.doDeleteForum(id);
    }, 'Нет', function() {
      curBox().hide();
    });
  },

  doDeleteForum: function(id) {
    ajax.post('/discuss?act=delete&id='+ id, {}, function(r) {
      curBox().hide();
      boxPopup(r.msg);
      nav.go(r.url);
    }, function(r) {

    });
  },

  createTheme: function(id) {
    var title = $.trim($('#bnt_title').val()),
      post = $.trim($('#bnt_post').val()),
      attaches = [];

    if (!title) {
      $('#bnt_title').focus();
      return false;
    }

    if (!post) {
      $('#bnt_post').focus();
      return false;
    }

    if (A.discussThemeCreate) return;
    A.discussThemeCreate = true;

    $('input[type="hidden"][name*=attach]').each(function(i, item) {
      attaches.push($(item).val());
    });

    $('#bnt_progress').show();
    ajax.post('/discuss'+ id +'?act=create', {title: title, post: post, attaches: attaches}, function(r) {
      $('#bnt_progress').hide();
      A.discussThemeCreate = false;

      nav.go(r.url);
    }, function(xhr) {
      $('#bnt_progress').hide();
      A.discussThemeCreate = false;
    });
  },

  replyPost: function(post_id) {
    A.discussPostFixed = true;
    Discuss.scroll();
    $('#discuss_text').focus().val($('#discuss_text').val() + '[post'+ post_id +'|'+ $('#discuss_post'+ post_id + ' a.discuss_post_author').attr('data-name') +'], ').keyup();
  },

  fixPost: function() {
    A.discussPostTmHeight = setInterval(Discuss.ctrlPost, 100);
    $(window).bind('scroll', Discuss.scroll);
  },
  ctrlPost: function() {
    $('#discuss_post_wrap').css({height: $('#discuss_post').outerHeight()});
  },

  scroll: function() {
    if (A.discussPostFixed) {
      var $win = $(window), $post = $('#discuss_post'), wY = $win.height() + $win.scrollTop(), fY = $('#discuss_fixer').offset().top;
      if (wY < (fY + $post.outerHeight())) {
        $post.addClass('fixed');
        $('#discuss_cancel').show();
      }
      else $post.removeClass('fixed');
    }
    else {
      $('#discuss_post').removeClass('fixed');
      $('#discuss_cancel').hide();
      $(window).unbind('scroll', Discuss.scroll);
      clearInterval(A.discussPostTmHeight);
    }
  },

  onKeyUp: function() {
    var post = $.trim($('#discuss_text').val());
    A.discussPostFixed = (post.length > 0) ? true : false;
    if (!A.discussPostFixed) Discuss.scroll();
    else Discuss.fixPost();
  },

  cancelPost: function() {
    $('#discuss_text').val('').keyup();
  },

  sendPost: function(fid) {
    var post = $.trim($('#discuss_text').val()), attaches = [];

    if (!post) {
      $('#discuss_text').focus();
      return false;
    }

    if (A.discussPostCreate) return;
    A.discussPostCreate = true;

    $('input[type="hidden"][name*=attach]').each(function(i, item) {
      attaches.push($(item).val());
    });

    $('#bnt_progress').show();
    ajax.post('/discuss'+ fid +'?act=create', {post: post, attaches: attaches, last_id: A.discussPostLastId}, function(r) {
      $('#bnt_progress').hide();
      A.discussPostCreate = false;

      nav.go('/discuss'+ fid +'?offset=last&scroll=1');
    }, function(xhr) {
      $('#bnt_progress').hide();
      A.discussPostCreate = false;
    });
  },

  attachPhoto: function(file_id) {
    if (A.discussPostPhotoAttaches >= 3) {
      boxPopup('Вы не можете прикрепить более 3-х фотографий');
      return;
    }

    cur['fileDoneCustom'+ file_id] = Discuss.onUploadDone.pbind(file_id);
    Upload.onStart(file_id);
  },

  onUploadDone: function(file_id, filedata) {
    var json = $.parseJSON(filedata);
    Upload.showInput(file_id);
    Upload.initFile(file_id, function() {
      Discuss.attachPhoto(file_id);
    });

    $('<input/>').attr({type: 'hidden', id: file_id +'_attach', name: 'DiscussPost[attach][]'}).val(filedata).prependTo('#bnt_attaches');
    var cont = $('<div/>').attr({class: 'left post_attach_photo'}).appendTo('#bnt_attaches');
    cont.html('<img src="http://cs'+ json['b'][2] +'.'+ A.host +'/'+ json['b'][0] +'/'+ json['b'][1] +'" alt=""/><a class="tt photo_attach_delete" title="Удалить фотографию"><span class="icon-remove icon-white"></span></a>');
    if (A.discussPostPhotoAttaches == null) A.discussPostPhotoAttaches = 0;
    A.discussPostPhotoAttaches++;

    if (A.discussPostPhotoAttaches >= 3) {
      $('#file_button_'+ file_id).hide();
    }

    cont.children('a').click(function() {
      $('#file_button_'+ file_id).show();
      A.discussPostPhotoAttaches--;
      if (A.discussPostPhotoAttaches < 0) A.discussPostPhotoAttaches = 0;
      cont.remove();
      $('#'+ file_id +'_attach').remove();
    });
  },

  editPost: function(post_id) {

  }
};

try {stmgr.loaded('discuss.js');}catch(e){}