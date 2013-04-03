var mail = {
  selectAll: function() {
    mail.deselectAll();

    $('#messages input[type="checkbox"]').attr('checked', (!A.mailAllChecked));
    A.mailAllChecked = !A.mailAllChecked;
    A.mailReadedChecked = false;
    A.mailNewChecked = false;

    (A.mailAllChecked) ? mail.showMailActions() : mail.hideMailActions();
  },
  deselectAll: function() {
    $('#messages input[type="checkbox"]').attr('checked', false);
  },
  selectReaded: function() {
    mail.deselectAll();

    $('#messages tr[read="1"] input[type="checkbox"]').attr('checked', (!A.mailReadedChecked));
    A.mailReadedChecked = !A.mailReadedChecked;
    A.mailAllChecked = false;
    A.mailNewChecked = false;

    (A.mailReadedChecked) ? mail.showMailActions() : mail.hideMailActions();
  },
  selectNew: function() {
    mail.deselectAll();

    $('#messages tr[read!="1"] input[type="checkbox"]').attr('checked', (!A.mailNewChecked));
    A.mailNewChecked = !A.mailNewChecked;
    A.mailReadedChecked = false;
    A.mailAllChecked = false;

    (A.mailNewChecked) ? mail.showMailActions() : mail.hideMailActions();
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

    $('#mail_summary').text(A.mailSummary);
    A.mailSummary = null;
  },

  _reset: function() {
    A.mailNewChecked = null; A.mailReadedChecked = null; A.mailAllChecked = null; A.mailSummary = null;
  },

  getSelected: function() {
    return $('#messages input[type="checkbox"]:checked');
  },
  deleteSelected: function() {
    var $list = mail.getSelected();
  },

  markAsReaded: function() {
    var $list = mail.getSelected(), items = [];
    $.each($list, function(i,item) {
      items.push(parseInt($(item).val()));
    });

    ajax.post('/mail?act=mark_readed', {items: items}, function(r) {

    }, function(xhr) {

    });
  },
  markAsNew: function() {
    var $list = mail.getSelected();
    $.each($list, function(i,item) {
      items.push(parseInt($(item).val()));
    });

    ajax.post('/mail?act=mark_new', {items: items}, function(r) {

    }, function(xhr) {

    });
  }
};

try {stmgr.loaded('mail.js');}catch(e){}