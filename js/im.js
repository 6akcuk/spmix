var Im = {
    selectDialog: function(dialog_id, event) {
        nav.go('/im?sel='+ dialog_id, event);
    },

    showSendButton: function() {
        $('#im_send').html('Отправить');
    },

    sendMessage: function() {
        var $im = $('#imform'),
            recipients = $im.find('input[name^="im_wdd"]'),
            rec = [],
            title = $.trim($im.find('[name="Im[title]"]').val()),
            message = $.trim($im.find('[name="Im[message]"]').val());

        if (recipients.length == 0) {
            WideDropdown.show('im_wdd', event);
            return false;
        }
        else if (recipients.length > 1 && !title) {
            report_window.create($im.find('[name="Im[title]"]'), 'left', 'Требуется название для беседы');
            return false;
        }
        else if (recipients.length > 0 && !message) {
            report_window.create($im.find('[name="Im[message]"]'), 'left', 'Напишите хоть что-нибудь');
            return false;
        }

        if (A.imSending) return;
        A.imSending = true;

        $('#im_send').html('<img src="/images/progress_small.gif" />');
        for(var i=0; i < recipients.length; i++) {
            rec.push(parseInt(recipients[i].value));
        }

        ajax.post('/im?sel=-1', {recipients: rec, title: title, message: message}, function(r) {
            A.imSending = false;
            if (r.success) nav.go(r.url, null);
            else report_window.create($im, 'left', r.message);

            Im.showSendButton();
        }, function(xhr) {
            A.imSending = false;
            Im.showSendButton();
        });
    },

    setup: function(dialog_id) {
        var $head = $('#header');

        $('body').addClass('im_fixed');
        $('#sidebar').css({top: $head.outerHeight()});
        $('#im_nav').css({top: $head.outerHeight()});
        $('#im_content').css({padding:
            ($head.outerHeight() + $('#im_nav').outerHeight()) +'px 0px '+
            ($('#im_controls').outerHeight()) + 'px'
        });
    }
};

try {stmgr.loaded('im.js');}catch(e){}