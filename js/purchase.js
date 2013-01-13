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
    },

    restore: function(id) {
        ajax.post('/purchase'+ id +'/restore', null, function(r) {
            nav.go(r.url, null, null);
        });
    },

    addgood: function() {
        FormMgr.submit('#addgoodform', 'right', function(r) {
            nav.go(r.url, null, null);
        });
        return false;
    },

    editgood: function() {
        FormMgr.submit('#purchaseform', 'right', function(r) {
            nav.go(r.url, null, null);
        });
        return false;
    },

    goEditGood: function(c, good, event) {
        A.glCancelClick = true;

        event.preventDefault();
        event.stopPropagation();

        var sp = good.split('_'),
            purchase_id = sp[0],
            good_id = sp[1];

        nav.go('/good'+ purchase_id +'_'+ good_id +'/edit', null);
        return false;
    },

    deletegood: function(c, good, event) {
        event.preventDefault();
        event.stopPropagation();

        var sp = good.split('_'),
            purchase_id = sp[0],
            good_id = sp[1];

        $(c).mouseleave();

        var $a = $('#good'+ good + ' a.good_row_relative'), $m;
        $a.addClass('good_row_deleted');
        $m = $('<div class="good_row_deleted_msg"></div>').appendTo($a);

        ajax.post('/good'+ purchase_id +'_'+ good_id +'/delete', null, function(r) {
            $m.html(r.html);
            $m.css({top: ($a.outerHeight() - $m.outerHeight()) / 2});
        });
    },

    restoregood: function(purchase_id, good_id) {
        A.glCancelClick = true;

        ajax.post('/good'+ purchase_id +'_'+ good_id +'/restore', null, function(r) {
            if (r.success) {
                var $a = $('#good'+ purchase_id +'_'+ good_id + ' a.good_row_relative');
                $a.removeClass('good_row_deleted');
                $a.find('div.good_row_deleted_msg').remove();
            }
            else ajex.show(r.html);
        });
    },

    overGood: function(c, event) {
        if (!$(c).hasClass('good_row_deleted')) $(c).find('div.good_row_controls').show();
    },

    outGood: function(c, event) {
        $(c).find('div.good_row_controls').hide();
    },

    overIcon: function(c) {
        $(c).css({opacity: 1});
    },
    outIcon: function(c) {
        $(c).css({opacity: 0.8});
    },

    uploadGoodImage: function(purchase_id, good_id, file_id) {
        cur['fileDoneCustom'+ file_id] = Purchase.onUploadDone.pbind(purchase_id, good_id, file_id);
        Upload.onStart(file_id);
    },
    onUploadDone: function(purchase_id, good_id, file_id, filedata) {
        var json = $.parseJSON(filedata);
        Upload.showInput(file_id);
        Upload.initFile(file_id, function() {
            Purchase.uploadGoodImage(purchase_id, good_id, file_id);
        });

        ajax.post('/goods/addimage', {purchase_id: purchase_id, good_id: good_id, image: filedata}, function(r) {
            if (r.success) {
                var cont = $('<div/>').attr({class: 'left good_image'}).appendTo('#images_list');
                cont.html('<img src="http://cs'+ json['b'][2] +'.spmix.ru/'+ json['b'][0] +'/'+ json['b'][1] +'" alt=""/><a class="tt iconify_x_a" title="Удалить изображение"></a>');

                msi.show('Изображение успешно добавлено в галерею');

                cont.children('a').click(function() {
                    Purchase.removeImage.call(this, purchase_id, good_id, r.id);
                });
            }
            else {
                var msg = [];
                $.each(r, function(i, v) {
                    msg.push(v);
                });
                report_window.create($('#images_list'), 'top', msg.join('<br/>'));
            }
        }, function(xhr) {
            ajex.show(xhr.responseText);
        });
    },
    removeImage: function(purchase_id, good_id, image_id) {
        var $this = $(this);
        ajax.post('/goods/removeimage', {purchase_id: purchase_id, good_id: good_id, image_id: image_id}, function(r) {
            $this.parent().remove();
        });
    },

    showImages: function(good_id) {
        ajax.post('/goods/getImages', {good_id: good_id}, function(list) {
            Photoview.list('good'+ good_id, list);
            Photoview.show('good'+ good_id);
        });
    },

    saveOic: function() {
        FormMgr.submit('#oicform', 'right', function(r) {
            msi.show(r.msg);
            nav.go(r.url, null, null);
        });
        return false;
    },

    order: function() {
        FormMgr.submit('#orderform', 'right', function(r) {
            msi.show(r.msg);
            nav.go(r.url, null, null);
        });
        return false;
    }
};

try {stmgr.loaded('purchase.js');}catch(e){}