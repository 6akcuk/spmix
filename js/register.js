/**
 * Created with JetBrains PhpStorm.
 * User: denis
 * Date: 21.11.12
 * Time: 17:39
 * To change this template use File | Settings | File Templates.
 */
var register = {
    sended: null,

    next: function() {
        FormMgr.submit('#regform', 'left', function(r) {
            if (r.step < 4) nav.go('/register/step'+ (r.step+1), null, {});
            else location.href = '/id'+ r.id;
        });
    },
    sendCode: function(force) {
        var v = $('#RegisterForm_phone').val();
        if (v != '' && (!register.sended || register.sended != v || force)) {
            $('#sendCodeLink').show();

            ajax.post('/register/sendSMS', {phone: v}, function(r) {
                register.sended = v;
                msi.show(r.message);
            });
        }
    }
};

try {stmgr.loaded('register.js');}catch(e){}