var Purchase = {
    create: function() {
        FormMgr.submit('#purchaseform', 'right', function(r) {
            nav.go(r.url, null, null);
        });
        return false;
    },

    edit: function() {
        FormMgr.submit('#purchaseform', 'right', function(r) {
            nav.go(r.url, null, null);
        });
        return false;
    }
};