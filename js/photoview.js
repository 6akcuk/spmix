var Photoview = {
    beforeShow: function() {},

    setupLayout: function() {
        A.pvBackground = $('#layout_bg');
        A.pvLayoutWrap = $('#layout_wrap');
        A.pvLayout = $('#layout');

        A.pvLayout.html('\
<div class="pv_content">\
<table cellspacing="0" cellpadding="0">\
<tr><td class="sidesh s1"><div></div></td><td>\
<table cellspacing="0" cellpadding="0">\
<tr><td class="sidesh s2"><div></div></td><td>\
<table cellspacing="0" cellpadding="0">\
<tr><td class="sidesh s3"><div></div></td><td>\
\
<div id="pv_box">\
    <a class="pull-right pv_close_link" onclick="Photoview.hide()">Закрыть</a>\
    <div id="pv_summary"></div>\
    <div id="pv_photo_wrap">\
        <div id="pv_loader"></div>\
        <a id="pv_photo" style="display:none;" onclick="Photoview.next()"></a>\
        <div id="pv_html" class="clearfix" style="display:none;"></div>\
    </div>\
</div>\
\
</td><td class="sidesh s3"><div></div></td></tr>\
<tr><td colspan="3" class="bottomsh s3"><div></div></td></tr></table>\
</td><td class="sidesh s2"><div></div></td></tr>\
<tr><td colspan="3" class="bottomsh s2"><div></div></td></tr></table>\
</td><td class="sidesh s1"><div></div></td></tr>\
<tr><td colspan="3" class="bottomsh s1"><div></div></td></tr></table>\
</div>\
<div id="pv_left_nav" onmouseover="Photoview.activate(A.pvLeft)" onmouseout="Photoview.deactivate(A.pvLeft)" onmousedown="Photoview.prev()"></div>\
<div id="pv_right_nav" onmouseover="Photoview.activate(A.pvClose)" onmouseout="Photoview.deactivate(A.pvClose)" onmousedown="Photoview.hide()"></div>');

        $('\
<div class="pv_fixed fixed">\
    <div class="pv_left" onmouseover="Photoview.activate(this)" onmouseout="Photoview.deactivate(this)" onmousedown="Photoview.prev()"><div></div></div>\
    <div class="pv_close" onmouseover="Photoview.activate(this)" onmouseout="Photoview.deactivate(this)" onmousedown="Photoview.hide()"><div></div></div>\
</div>\
').appendTo('body');

        $.extend(A, {
            pvBox: $('#pv_box'),
            pvSummary: $('#pv_summary'),
            pvLoader: $('#pv_loader'),
            pvPhoto: $('#pv_photo'),
            pvHtml: $('#pv_html'),

            pvLeftNav: $('#pv_left_nav'),
            pvRightNav: $('#pv_right_nav'),
            pvLeft: $('div.pv_left'),
            pvClose: $('div.pv_close')
        });
    },

    list: function(listId, data) {
        if (!A.pvList) A.pvList = {};
        A.pvList[listId] = data;
    },

    show: function(listId, photo) {
        if (!A.pvLayout) {
            Photoview.setupLayout();
        }

        if (!A.pvList || !A.pvList[listId]) {
            return;
        }

        listId = A.pvList[listId];

        $.extend(A, {
            pvCount: listId.count,
            pvBase: listId.base,
            pvItems: listId.items
        });

        A.pvOffset = 0;

        $.each(A.pvItems, function(i, item) {
            if (item.a[1] == photo) {
                A.pvOffset = i;
                return false;
            }
        });

        $win = $(window);
        $(document.body).css({
            overflow: 'hidden',
            cursor: 'default'
        });
        A.pvBackground.show().css({
            height: $win.height()
        });
        A.pvLayoutWrap.show().css({
            width: $win.width(),
            height: $win.height()
        });
        A.pvLeft.show().css({
            opacity: 0.4
        });
        A.pvClose.show().css({
            opacity: 0.4,
            left: $win.width() - 57
        });
        A.pvFirstStart = true;

        A.pvSummary.html('<div class="loader"/>');
        Photoview.doShow();
    },

    hide: function() {
        $(document.body).css({
            overflow: 'auto'
        });
        A.pvBackground.hide();
        A.pvLayoutWrap.hide();
        A.pvLeft.hide();
        A.pvClose.hide();
    },

    doShow: function() {
        if (!A.pvList || !A.pvItems || (A.pvCount && typeof A.pvOffset == 'undefined')) {
            return;
        }

        if (!A.pvFirstStart) Photoview.beforeShow();
        A.pvPhoto.html('<img src="'+ Photoview.genUrl() +'" alt="" />').children().first().load(Photoview.doImageLoaded);

        A.pvFirstStart = false;
    },

    doImageLoaded: function() {
        var hw = ($win.width() - A.pvBox.width()) / 2;
        A.pvLeftNav.css({
            width: hw - 20,
            height: $win.height() - 42
        });
        A.pvRightNav.css({
            left: hw + A.pvBox.width(),
            width: hw,
            height: $win.height() - 42
        });

        A.pvSummary.html('Фотография '+ (A.pvOffset+1) +' из '+ A.pvCount);
        A.pvLoader.hide();
        A.pvPhoto.show();
        A.pvHtml.show();
    },

    genUrl: function() {
        //var path = A.pvItems[A.pvOffset].x //A.pvItems[A.pvOffset].src.split('/');
        var item = A.pvItems[A.pvOffset].w;
        return 'http://cs'+ item[2] +'.spmix.ru/'+ item[0] +'/'+ item[1];
    },

    prev: function() {
        A.pvOffset = (A.pvOffset == 0) ? (A.pvCount - 1) : A.pvOffset - 1;
        Photoview.doShow();
    },

    next: function() {
        A.pvOffset = (A.pvOffset == (A.pvCount - 1)) ? 0 : A.pvOffset + 1;
        Photoview.doShow();
    },

    activate: function(arrow) {
        $(arrow).stop().animate({opacity: 1}, 200);
    },

    deactivate: function(arrow) {
        $(arrow).stop().animate({opacity: 0.4}, 200);
    },
    // много прям html ;)
    html: function(html) {
        A.pvHtml.html(html);
    }
};

try {stmgr.loaded('photoview.js');}catch(e){}