var Profile = {

    showAddFriend: function($cont, friend_id) {
        $cont = $($cont);
        $cont.replaceWith('<a class="button" onclick="return Profile.addFriend(this, '+ friend_id +')">Добавить в друзья</a>');
    },
    showDeleteFriend: function() {
        $cont = $($cont);
        $cont.replaceWith('<a onclick="return Profile.addFriend(this, '+ friend_id +')">Убрать из друзей</a>');
    },
    showKeepSubscriber: function() {
        $cont = $($cont);
        $cont.replaceWith('<a onclick="return Profile.keepSubscriber(this, '+ friend_id +')">Оставить в подписчиках</a>');
    },

    addFriend: function(cont, friend_id) {
        if (A.frAdd) return;
        A.frAdd = true;

        var $cont = $(cont);
        $cont.html('<img src="/images/progress_small.gif" />');

        ajax.post('/friends/add', {friend_id: friend_id}, function(r) {
            A.frAdd = false;

            if (r.success) {
                if ($cont.next().hasClass('social-status')) {
                    $cont.next().html(r.message);
                    $cont.remove();
                }
                else $cont.replaceWith('<div class="social-status">'+ r.message +'</div>');
            }
            else {
                ajex.show(r.message);
                Profile.showAddFriend($cont, friend_id);
            }
        }, function(r) {
            A.frAdd = false;
            Profile.showAddFriend($cont, friend_id);
        });
    },

    deleteFriend: function(cont, friend_id) {
        if (A.frDelete) return;
        A.frDelete = true;

        var $cont = $(cont);
        $cont.html('<img src="/images/progress_small.gif" />');

        ajax.post('/friends/delete', {friend_id: friend_id}, function(r) {
            A.frDelete = false;

            if (r.success) {
                $('#people'+ friend_id).addClass('report').html(r.message);
            }
            else {
                ajex.show(r.message);
                Profile.showDeleteFriend($cont, friend_id);
            }
        }, function(r) {
            A.frDelete = false;
            Profile.showDeleteFriend($cont, friend_id);
        });
    },

    keepSubscriber: function(cont, friend_id) {
        if (A.frKeep) return;
        A.frKeep = true;

        var $cont = $(cont);
        $cont.html('<img src="/images/progress_small.gif" />');

        ajax.post('/friends/keep', {friend_id: friend_id}, function(r) {
            A.frKeep = false;

            if (r.success) {
                $('#people'+ friend_id).addClass('report').html(r.message);
            }
            else {
                ajex.show(r.message);
                Profile.showKeepSubscriber($cont, friend_id);
            }
        }, function(r) {
            A.frKeep = false;
            Profile.showKeepSubscriber($cont, friend_id);
        });
    },

  showStatusEditor: function(cont) {
    var $w = $('#profile-status-editor'), st = $.trim($('#profile-status').text());
    $w.show().css({
      top: $(cont).offset().top - $('#content').offset().top - 12 - 8,
      left: $(cont).offset().left - $('#content').offset().left - 12 - 8
    });
    $w.click(function(event) {
      event.stopPropagation();
    });
    $w.find('input').focus().val((st != 'Изменить статус') ? st : '');

    setTimeout(function() {
      $('body').one('click', function() {
        $w.hide();
      });
    }, 1);
  },

  saveStatus: function() {
    var status = $.trim($('#profile-status-editor input[type="text"]').val());
    ajax.post('/users/profiles/status', {status: status}, function(r) {
      $('body').click();
      if (status == '') status = 'Изменить статус';
      $('#profile-status').text(status);
    });
  },

    incReputation: function(cont, user_id) {
        var $w = $('#rep_pos_box');
        $w.show().css({
            top: $(cont).offset().top - $('#content').offset().top - $w.outerHeight() - 10,
            left: $(cont).offset().left - $('#content').offset().left + ($(cont).outerWidth() - $w.outerWidth()) / 2
        });
        $w.click(function(event) {
            event.stopPropagation();
        });

        setTimeout(function() {
            $('body').one('click', function() {
                $w.hide();
            });
        }, 1);
    },

    decReputation: function(cont, user_id) {
        var $w = $('#rep_neg_box');
        $w.show().css({
            top: $(cont).offset().top - $('#content').offset().top - $w.outerHeight() - 10,
            left: $(cont).offset().left - $('#content').offset().left + ($(cont).outerWidth() - $w.outerWidth()) / 2
        });
        $w.click(function(event) {
            event.stopPropagation();
        });

        setTimeout(function() {
            $('body').one('click', function() {
                $w.hide();
            });
        }, 1);
    },

    doIncReputation: function(user_id) {
        var val = parseInt($('#rep_pos_box input[name="rep_value"]:checked').val()),
            com = $.trim($('#rep_pos_box textarea').val());

      if ($('#rep_pos_box input[type="text"][name="rep_value"]').val())
        val = parseInt($('#rep_pos_box input[type="text"][name="rep_value"]').val());

        if (!val || !com) return;
        if (A.repInc) return;
        A.repInc = true;

        ajax.post('/reputation'+ user_id + '?act=increase', {value: val, comment: com}, function(r) {
            A.repInc = false;
            $('body').click();
            $('#pos_rep_value').html(r.positive_rep);
        }, function(xhr) {
            A.repInc = false;
            $('body').click();
        });
    },

    doDecReputation: function(user_id) {
        var val = parseInt($('#rep_neg_box input[name="rep_value"]:checked').val()),
            com = $.trim($('#rep_neg_box textarea').val());

      if ($('#rep_neg_box input[type="text"][name="rep_value"]').val())
        val = parseInt($('#rep_neg_box input[type="text"][name="rep_value"]').val());

      if (!val || !com) return;
        if (A.repDec) return;
        A.repDec = true;

        ajax.post('/reputation'+ user_id + '?act=decrease', {value: val, comment: com}, function(r) {
            A.repDec = false;
            $('body').click();
            $('#neg_rep_value').html(r.negative_rep);
        }, function(xhr) {
            A.repDec = false;
            $('body').click();
        });
    },

    deleteReputation: function(id) {
        if (A.repDelete) return;
        A.repDelete = true;

        ajax.post('/users/profiles/deleteReputation', {id: id}, function(r) {
            A.repDelete = false;

            var $p = $('#rep'+ id),
                $h = $('<div/>').attr({class: 'reputation_hider'}).appendTo($p),
                $t = $('<div/>').attr({class: 'reputation_hider_tag'}).appendTo($p);
            $h.css({width: $p.outerWidth(), height: $p.outerHeight()});
            $t.html(r.html).css({
                top: ($p.outerHeight() - $t.outerHeight()) / 2,
                left: ($p.outerWidth() - $t.outerWidth()) / 2
            });
        }, function(xhr) {
            A.repDelete = false;
        });
    },

    restoreReputation: function(id, hash) {
        if (A.repRestore) return;
        A.repRestore = true;

        ajax.post('/users/profiles/restoreReputation', {id: id, hash: hash}, function(r) {
            A.repRestore = false;

            var $p = $('#rep'+ id);
            $p.find('div.reputation_hider').remove();
            $p.find('div.reputation_hider_tag').remove();
        }, function(xhr) {
            A.repRestore = false;
        });
    },

  sendInvite: function() {
    FormMgr.submit('#profile-invite-form', 'left', function(r) {
      if (r.msg) boxPopup(r.msg);
      $('#phone').val(''); $('#name').val('');
    }, function(r) {

    });
  },

  changePassword: function() {
    FormMgr.submit('#changepwdform', 'left', function(r) {
      if (r.msg) boxPopup(r.msg);
      $('input[name*="ChangePasswordForm"]').val('').blur();
    }, function(r) {

    });
  },

  saveEmail: function() {
    FormMgr.submit('#changeemailform', 'left', function(r) {
      if (r.msg) boxPopup(r.msg);
      $('input[name*="ChangeEmailForm"]').val('').blur();
    }, function(r) {

    });
  },

  changePhone: function() {
    showGlobalPrg();

    ajax.post('/settings', {act: 'changephone'}, function(r) {
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

  getPhoneCode: function() {
    var $form = $('#changephoneform');

    FormMgr.submit($form, 'left', function(r) {
      if (r.msg) boxPopup(r.msg);
      if (r.step == 1) {
        $('#change_phone_code').slideDown();
        $('#change_phone_button').html('Сменить номер');
        $form.find('input[name="eid"]').val(r.eid);
      }
      else if (r.step == 2) {
        curBox().hide();
      }
    }, function(r) {

    });
  },

  emailNotify: function() {
    FormMgr.submit('#emailnotifyform', 'left', function(r) {
      if (r.msg) boxPopup(r.msg);
    }, function(r) {

    });
  }
};

try {stmgr.loaded('profile.js');}catch(e){}