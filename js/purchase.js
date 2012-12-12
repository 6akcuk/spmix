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

    uploadGoodImage: function(purchase_id, good_id, file_id) {
        cur['fileDoneCustom'+ file_id] = Purchase.onUploadDone.pbind(purchase_id, good_id, file_id);
        Upload.onStart(file_id);
    },
    onUploadDone: function(purchase_id, good_id, file_id, filedata) {
        var json = $.parseJSON(filedata),
            cont = $('<div/>').attr({class: 'left good_image'}).appendTo('#images_list');
        Upload.showInput(file_id);
        Upload.initFile(file_id, function() {
            Purchase.uploadGoodImage(purchase_id, good_id, file_id);
        });

        cont.html('<img src="http://cs'+ json['b'][2] +'.spmix.ru/'+ json['b'][0] +'/'+ json['b'][1] +'" alt=""/><a class="tt iconify_x_a" title="Удалить изображение"></a>');
        ajax.post('/goods/addimage', {purchase_id: purchase_id, good_id: good_id, image: filedata}, function(r) {
            msi.show('Изображение успешно добавлено в галерею');

            cont.children('a').click(function() {
                Purchase.removeImage.call(this, purchase_id, good_id, r.id);
            });
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
    }
};

try {stmgr.loaded('purchase.js');}catch(e){}