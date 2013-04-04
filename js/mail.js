var mail = {
  selectAll: function() {
    mail.deselectAll();
    var list = $('#messages input[type="checkbox"]');
    if (list.length == 0) return;

    list.attr('checked', (!A.mailAllChecked));
    A.mailAllChecked = !A.mailAllChecked;
    A.mailReadedChecked = false;
    A.mailNewChecked = false;

    (A.mailAllChecked) ? mail.showMailActions() : mail.hideMailActions();
  },
  deselectAll: function() {
    $('#messages input[type="checkbox"]').attr('checked', false);
    mail.hideMailActions();
  },
  selectReaded: function() {
    mail.deselectAll();
    var list = $('#messages tr[read="1"] input[type="checkbox"]');
    if (list.length == 0) return;

    list.attr('checked', (!A.mailReadedChecked));
    A.mailReadedChecked = !A.mailReadedChecked;
    A.mailAllChecked = false;
    A.mailNewChecked = false;

    (A.mailReadedChecked) ? mail.showMailActions() : mail.hideMailActions();
  },
  selectNew: function() {
    mail.deselectAll();
    var list = $('#messages tr[read!="1"] input[type="checkbox"]');
    if (list.length == 0) return;

    list.attr('checked', (!A.mailNewChecked));
    A.mailNewChecked = !A.mailNewChecked;
    A.mailReadedChecked = false;
    A.mailAllChecked = false;

    (A.mailNewChecked) ? mail.showMailActions() : mail.hideMailActions();
  },
  select: function() {
    (mail.getSelected().length) ? mail.showMailActions() : mail.hideMailActions();
  },
  showMailActions: function() {
    $('#mail_search').hide();
    $('#mail_actions').show();

    if (!A.mailSummary) A.mailSummary = $('#mail_summary').text();
    $('#mail_summary').text('Выделено сообщений: '+ mail.getSelected().length);
  },
  hideMailActions: function() {
    $('#mail_search').show();
    $('#mail_actions').hide();

    if (A.mailSummary) $('#mail_summary').text(A.mailSummary);
    A.mailSummary = null;
  },

  _reset: function() {
    A.mailNewChecked = null; A.mailReadedChecked = null; A.mailAllChecked = null; A.mailSummary = null;
  },

  getSelected: function() {
    return $('#messages input[type="checkbox"]:checked');
  },
  deleteSelected: function() {
    var $list = mail.getSelected(), items = [];
    $.each($list, function(i,item) {
      items.push(parseInt($(item).val()));
    });

    ajax.post('/mail?act=delete_selected', {items: items}, function(r) {
      mail.onDeleteMsg(r);
    }, function(xhr) {

    });
  },
  deleteMsg: function(msg_id) {
    if (A.mailDeletingMsg && A.mailDeletingMsg[msg_id]) return;
    if (!A.mailDeletingMsg) A.mailDeletingMsg = {};
    A.mailDeletingMsg[msg_id] = true;

    $('#mess'+ msg_id +'_del').html('<img src="/images/upload.gif" />');
    ajax.post('/mail?act=delete_selected', {items: [msg_id]}, function(r) {
      A.mailDeletingMsg[msg_id] = null;
      mail.onDeleteMsg(r);
    }, function(xhr) {
      A.mailDeletingMsg[msg_id] = null;
      $('#mess'+ msg_id +'_del').html('Удалить');
    });
  },
  onDeleteMsg: function(r) {
    if (r.success) {
      if (!A.mailCache) A.mailCache = {};
      updMessCounter(r.pm);
      $.each(r.backlist, function(i, item) {
        $('#mess'+ item).removeClass('new_msg').addClass('mail_del_row').attr('read', '1');
        A.mailCache[item] = $('#mess'+ item + ' td.mail_contents').html();
        $('#mess'+ item +' td.mail_contents').html('Сообщение удалено. <a onclick="mail.restoreMsg('+ item +')" onmousedown="event.cancelBubble = true;">Восстановить</a>');
        $('#mess'+ item +'_del').html('');
      });
    }
  },

  restoreMsg: function(msg_id) {
    if (A.mailRestoringMsg && A.mailRestoringMsg[msg_id]) return;
    if (!A.mailRestoringMsg) A.mailRestoringMsg = {};
    if (!A.mailCacheRestore) A.mailCacheRestore = {};
    A.mailRestoringMsg[msg_id] = true;

    $msg = $('#mess'+ msg_id +' td.mail_contents');
    A.mailCacheRestore[msg_id] = $msg.html();

    $msg.html('<img src="/images/upload.gif" />');
    ajax.post('/mail?act=restore&id='+ msg_id, {}, function(r) {
      if (r.success) {
        $msg.html(A.mailCache[msg_id]);
        $('#mess'+ msg_id).removeClass('mail_del_row');
        $('#mess'+ msg_id +'_del').html('Удалить');
        A.mailCache[msg_id] = null;
      }
      else {
        $msg.html(A.mailCacheRestore[msg_id]);
        A.mailCacheRestore[msg_id] = null;
      }
      A.mailRestoringMsg[msg_id] = null;
    }, function(xhr) {
      $msg.html(A.mailCacheRestore[msg_id]);
      A.mailCacheRestore[msg_id] = null;
      A.mailRestoringMsg[msg_id] = null;
    });
  },

  markAsReaded: function() {
    var $list = mail.getSelected(), items = [];
    $.each($list, function(i,item) {
      items.push(parseInt($(item).val()));
    });

    ajax.post('/mail?act=mark_readed', {items: items}, function(r) {
      if (r.success) {
        updMessCounter(r.pm);
        $.each(r.backlist, function(i, item) {
          $('#mess'+ item).removeClass('new_msg').attr('read', '1');
        });
      }
    }, function(xhr) {

    });
  },
  markAsNew: function() {
    var $list = mail.getSelected(), items = [];
    $.each($list, function(i,item) {
      items.push(parseInt($(item).val()));
    });

    ajax.post('/mail?act=mark_new', {items: items}, function(r) {
      if (r.success) {
        updMessCounter(r.pm);
        $.each(r.backlist, function(i, item) {
          $('#mess'+ item).addClass('new_msg').attr('read', '');
        });
      }
    }, function(xhr) {

    });
  },

  showHistory: function(dialog_id) {
    if (A.mailHistory) return;
    A.mailHistory = dialog_id;
    A.mailHistoryCache = $('#mail_history_open').html();
    $('#mail_history_open').html('<img src="/images/upload.gif" />');
    ajax.post('/mail?act=history&id='+ dialog_id, {}, function(r) {
      A.mailHistory = null;
      $('#mail_history').html(r.html);
    }, function(xhr) {
      A.mailHistory = null;
      $('#mail_history_open').html(A.mailHistoryCache);
    });
  },

  attachPhoto: function(file_id) {
    if (A.mailPhotoAttaches >= 3) {
      boxPopup('Вы не можете прикрепить более 3-х фотографий');
      return;
    }

    cur['fileDoneCustom'+ file_id] = mail.onUploadDone.pbind(file_id);
    Upload.onStart(file_id);
  },

  onUploadDone: function(file_id, filedata) {
    var json = $.parseJSON(filedata);
    Upload.showInput(file_id);
    Upload.initFile(file_id, function() {
      mail.attachPhoto(file_id);
    });

    $('<input/>').attr({type: 'hidden', id: file_id +'_attach', name: 'Mail[attach][]'}).val(filedata).prependTo('#mail_form');
    var cont = $('<div/>').attr({class: 'left mail_attach_photo'}).appendTo('#mail_attaches');
    cont.html('<img src="http://cs'+ json['b'][2] +'.'+ A.host +'/'+ json['b'][0] +'/'+ json['b'][1] +'" alt=""/><a class="tt photo_attach_delete" title="Удалить фотографию"><span class="icon-remove icon-white"></span></a>');
    if (A.mailPhotoAttaches == null) A.mailPhotoAttaches = 0;
    A.mailPhotoAttaches++;

    if (A.mailPhotoAttaches >= 3) {
      $('#file_button_'+ file_id).hide();
    }

    cont.children('a').click(function() {
      $('#file_button_'+ file_id).show();
      A.mailPhotoAttaches--;
      if (A.mailPhotoAttaches < 0) A.mailPhotoAttaches = 0;
      cont.remove();
      $('#mail_'+ file_id +'_attach').remove();
    });
  },
};

try {stmgr.loaded('mail.js');}catch(e){}