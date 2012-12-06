var UserMgr = {
    assignRole: function(el) {
        olcm.show(el, rolesList, UserMgr.roleAssigned);
    },
    roleAssigned: function(el, item) {
        var id = parseInt($(el).attr('data-id'));

        ajax.post('/users/users/assignRole', {user_id: id, role: item}, function(r) {
            if (r.msg) msi.show(r.msg);
        });
    }
};