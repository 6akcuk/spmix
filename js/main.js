function show_progress() {
    var p = $('#global_progress');
    p.show().css({
        top: ($(window).height() - p.height()) / 3,
        left: ($(window).width() - p.width()) / 2
    });
}

/* Network */
$().ajaxError(function(xhr) {
    ajex.show('Общая ошибка соединения с сервером');
});

function test_rw() {
    report_window.create('#login-form', 'left', 'Неверный логин или пароль');
}

$().ready(function() {
    /* GSearch */
    $('#gsearch').focus(function() {
            $(this).parent().addClass('focused');
        }).blur(function() {
            $(this).parent().removeClass('focused');
        });
});

try {stmgr.loaded('main.js');}catch(e){}