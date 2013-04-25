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
function updOrdersCounter(num) {
  updMenuCounter('#orders_link', '/orders', num);
}
function updUsersCounter(num) {
  updMenuCounter('#users_link', '/users', num);
}
function updNewsCounter(num) {
  updMenuCounter('#news_link', '/feed?section=notifications', num);
}

function showGlobalPrg() {
  var $gp = $('#global_progress');
  $gp.show();
  $gp.css({
    top: ($(window).height() - $gp.height()) / 3,
    left: ($(window).width() - $gp.width()) / 2
  });
}
function hideGlobalPrg() {
  $('#global_progress').hide();
}

function onCtrlEnter(ev, handler) {
  ev = ev || window.event;
  if (ev.keyCode == 10 || ev.keyCode == 13 && (ev.ctrlKey || ev.metaKey)) {
    handler();
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