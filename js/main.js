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

$().ready(function() {
    $('input[name="cur_city"]').change(function() {
        ajax.post('/setcity', {city_id: $(this).val()}, function(r) {
            if (r.success) location.href = location.href;
        });
    });
});

try {stmgr.loaded('main.js');}catch(e){}