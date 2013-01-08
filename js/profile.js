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
    }
};

try {stmgr.loaded('profile.js');}catch(e){}