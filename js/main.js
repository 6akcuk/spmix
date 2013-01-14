function updMenuCounter(id, url, num) {
    var $cnt = $(id).find('a.right');
    if ($cnt.size()) {
        (num > 0) ? $cnt.html('+'+ num) : $cnt.remove();
    }
    else {
        if (num > 0)
            $('<a href="'+ url +'" onclick="return nav.go(this, event)" class="right lm-counter">+'+ num +'</a>').appendTo(id);
    }
}

function updFriendCounter(num) {
    updMenuCounter('#friends_link', '/friends?section=requests', num);
}
function updMessCounter(num) {
    updMenuCounter('#pm_link', '/im', num);
}
function updPurchaseCounter(num) {
    updMenuCounter('#ac_purchase_link', '/purchases/acquire', num);
}

$().ready(function() {
    $('#cur_city').change(function() {
        ajax.post('/setcity', {city_id: $(this).val()}, function(r) {
            if (r.success) nav.go(location.href, null);
        });
    });
});

try {stmgr.loaded('main.js');}catch(e){}