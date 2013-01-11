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

    setLogState: function(state, message_id) {
        var $ma = $('#ma'+ message_id),
            $ms = $('#mess'+ message_id);
        $ma.css('visibility', (state == 1) ? 'visible' : 'hidden');
        if (state == 1 && $ms.hasClass('im_in') && $ms.hasClass('im_new_msg')) {
            setTimeout(function() {
                Im.setMessViewed(message_id);
            }, 1);
        }
    },

    setMessViewed: function(message_id) {
        ajax.post('/im?action=viewed&id='+ message_id, null, function(r) {
            if (r.success) {
                $('#mess'+ message_id).removeClass('im_new_msg');
                updMessCounter(parseInt(r.counters['pm']));
            }
        }, function(r) {

        });
    },

    setup: function(dialog_id) {
        var $head = $('#header');
        A.imChecked = {};

        $('body').addClass('im_fixed');
        $('#sidebar').css({top: $head.outerHeight()});
        $('#im_nav').css({top: $head.outerHeight()});
        //$('#page_layout').css({marginTop: $head.outerHeight()});
        $('#im_content').css({padding:
            ($head.outerHeight() + $('#im_nav').outerHeight()) +'px 0px '+
            ($('#im_peer_controls').outerHeight()) + 'px'
        });

        Im.resizeRows();
        Im.pointControls();
    },

    pointControls: function() {
        var $sb = $('#sidebar'),
            $fill = $('#im_footer_filler'),
            lowY = $sb.offset().top - $(window).scrollTop() + $sb.outerHeight() + $('#im_peer_controls').outerHeight(),
            height, minY;

        height = ($(window).height < lowY) ? 0 : $(window).height() - lowY;
        minY = $('#header').outerHeight() + $('#im_nav').outerHeight() + 50;

        $fill.css({height: height});

        if ($fill.offset().top <= 0) {

        }
    },

    resizeRows: function() {
        var tHeight = $('#im_rows div.im_peer_rows').outerHeight();
        $('#im_rows').css({height: tHeight});
        $(window).scrollTop(tHeight);
    }
};

try {stmgr.loaded('im.js');}catch(e){}