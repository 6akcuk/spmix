function updFriendCounter(num) {
    var $friends_cnt = $('#friends_link').find('a.right');
    if ($friends_cnt.size()) {
        (num > 0) ? $friends_cnt.html('+'+ num) : $friends_cnt.remove();
    }
    else {
        if (num > 0)
            $('<a href="/friends?section=requests" onclick="return nav.go(this, event)" class="right lm-counter">+'+ num +'</a>').appendTo('#friends_link');
    }
}
function updMessCounter(num) {
    var $pm_cnt = $('#pm_link').find('a.right');
    if ($pm_cnt.size()) {
        (num > 0) ? $pm_cnt.html('+'+ num) : $pm_cnt.remove();
    }
    else {
        if (num > 0)
            $('<a href="/im" onclick="return nav.go(this, event)" class="right lm-counter">+'+ num +'</a>').appendTo('#pm_link');
    }
}

$().ready(function() {
    $('#cur_city').change(function() {
        ajax.post('/setcity', {city_id: $(this).val()}, function(r) {
            if (r.success) nav.go(location.href, null);
        });
    });
});

try {stmgr.loaded('main.js');}catch(e){}