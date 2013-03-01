var Comment = {
  add: function(hoop_id) {
    if (A.commentSending) return;
    $('#comment'+ hoop_id + '_progress').show();
    A.commentSending = true;

    var $form = $('#hoop'+ hoop_id + '_form');
    FormMgr.submit($form, null, function(r) {
      $('#comment'+ hoop_id + '_progress').hide();
      A.commentSending = false;

      $form.find('input[type="hidden"]').remove();
      $form.find('div.comment_attach_photo').remove();
      $form.find('textarea').val('').blur();
    }, function(xhr) {
      $('#comment'+ hoop_id + '_progress').hide();
      A.commentSending = false;
    });
  },

  attachPhoto: function(hoop_id, file_id) {
    if (A.commentPhotoAttaches >= 3) {
      boxPopup('Вы не можете прикрепить более 3-х фотографий');
      return;
    }

    cur['fileDoneCustom'+ file_id] = Comment.onUploadDone.pbind(hoop_id, file_id);
    Upload.onStart(file_id);
  },

  onUploadDone: function(hoop_id, file_id, filedata) {
    var json = $.parseJSON(filedata);
    Upload.showInput(file_id);
    Upload.initFile(file_id, function() {
      Comment.attachPhoto(hoop_id, file_id);
    });

    $('<input/>').attr({type: 'hidden', id: 'hoop'+ hoop_id +'_'+ file_id +'_attach', name: 'Comment[attach][]'}).val(filedata).prependTo('#hoop'+ hoop_id +'_form');
    var cont = $('<div/>').attr({class: 'left comment_attach_photo'}).appendTo('#hoop'+ hoop_id +'_attaches');
    cont.html('<img src="http://cs'+ json['b'][2] +'.'+ A.host +'/'+ json['b'][0] +'/'+ json['b'][1] +'" alt=""/><a class="tt photo_attach_delete" title="Удалить фотографию"><span class="icon-remove icon-white"></span></a>');
    A.commentPhotoAttaches++;

    cont.children('a').click(function() {
      A.commentPhotoAttaches--;
      if (A.commentPhotoAttaches < 0) A.commentPhotoAttaches = 0;
      cont.remove();
      $('#hoop'+ hoop_id +'_'+ file_id +'_attach').remove();
    });
  }
};

try {stmgr.loaded('comments.js');}catch(e){}