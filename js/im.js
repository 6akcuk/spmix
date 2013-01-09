var Im = {
    showSendButton: function() {
        $('#im_send').html('Отправить');
    },

    sendMessage: function() {
        var $im = $('#imform'),
            recipients = $im.find('[name*="im_wdd"]'),
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
        for(var i in recipients) {
            rec[i] = parseInt(recipients.val());
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
    }
};

try {stmgr.loaded('im.js');}catch(e){}