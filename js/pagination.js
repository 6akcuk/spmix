var Paginator = {
    opts: {},
    landmarks: [],

    init: function(opts) {
        var $pg = $('div.pagination'),
            ofs = $pg.offset();
        Paginator.opts = opts || {};

        if (!ofs) return;

        if (opts && opts.offset !== null) un.offset = un.offset || opts.offset;
        un.pgFixed = $('<div/>').addClass('pg_fixed')
            .attr('id', 'pg_fixed')
            .css({
                display: 'none'
            });
        un.pgFixed.html('<div class="pg_fixed_content"></div>');
        un.pgFixedContent = un.pgFixed.find('div.pg_fixed_content');

        un.pgFixed.appendTo(document.body);

        un.pgFixedTop = ofs.top;
        un.pgFixedLoaded = {};
        un.pgFixedLoaded[(un.offset + Paginator.opts.delta) / Paginator.opts.delta] = true;

        Paginator.initPages();
        Paginator.scanLandmarks();
        Paginator.attach();
    },

    initPages: function() {
        var page = (un.offset + Paginator.opts.delta) / Paginator.opts.delta,
            html = [],
            minPage = 1, maxPage = 1;

        if (page > 3) minPage = page - 2;
        maxPage = (page < Paginator.opts.pages - 2) ? page + 2 : Paginator.opts.pages;

        if (page > 3) html.push('<a class="pg_fixed_arr" href="'+ Paginator.opts.url +'?offset=0" onclick="return Paginator.nav(this, event)">&laquo;</a>');
        for(var i=minPage; i<=maxPage; i++) {
            var offset = i * Paginator.opts.delta - Paginator.opts.delta,
                cls = (offset == un.offset) ? 'pg_fixed_lnk_sel' : 'pg_fixed_lnk';
            if (cls == 'pg_fixed_lnk' && un.pgFixedLoaded[i]) cls = 'pg_fixed_lnk_fill';
            html.push('<a class="'+ cls +'" href="'+ Paginator.opts.url +'?offset='+ offset +'" onclick="return Paginator.nav(this, event)">'+ i +'</a>');
        }
        if (Paginator.opts.pages > maxPage) {
            var offset = maxPage * Paginator.opts.delta - Paginator.opts.delta;
            html.push('<a class="pg_fixed_arr" href="'+ Paginator.opts.url +'?offset='+ offset +'" onclick="return Paginator.nav(this, event)">&raquo;</a>');
        }

        un.pgFixedContent.html(html.join(''));
    },

    scanLandmarks: function() {
        $('li[data-paginator*="page"]').each(function(idx, val) {
            Paginator.landmarks[idx] = {top: $(this).offset().top, target: $(this)};
        });
    },

    attach: function() {
        $(window).on('scroll', Paginator.scrollResize);
    },

    scrollResize: function(evt) {
        var $win = $(window), st = $win.scrollTop() + $win.height(),
            $pgMore = $('a.pg_more'), mrt = $pgMore.offset().top;

        if (st > un.pgFixedTop && !un.pgFixedVisible) {
            un.pgFixed.stop().fadeIn();
            un.pgFixedVisible = true;
        }
        else if(st <= un.pgFixedTop && un.pgFixedVisible) {
            un.pgFixed.stop().fadeOut();
            un.pgFixedVisible = false;
        }

        if (!un.pgFixedNoMore && st >= mrt - 150) Paginator.preload();

        for (var i=0; i<Paginator.landmarks.length; i++) {
            var lm = Paginator.landmarks[i];

            if (st >= lm.top && (!Paginator.landmarks[i+1] || st < Paginator.landmarks[i+1].top)) {
                var pg = parseInt(lm.target.attr('data-paginator').split('-').pop()),
                    offset = (pg * Paginator.opts.delta) - Paginator.opts.delta;

                if (offset != un.offset) {
                    un.offset = offset;
                    //alert(st + ' ' + lm.top + ' '+ un.offset + ' '+ pg);
                    Paginator.initPages();
                }
            }
        }
    },

    preload: function() {
        var nextOffset = un.offset + Paginator.opts.delta,
            nextPage = (nextOffset + Paginator.opts.delta) / Paginator.opts.delta;

        if (un.pgFixedLoaded[nextPage]) return;
        un.pgFixedLoaded[nextPage] = true;

        if (nextPage == Paginator.opts.pages) {
            un.pgFixedNoMore = true;
            $('a.pg_more').hide();
        }

        $.post(Paginator.opts.url +'?offset='+ nextOffset, {}, function(r,s,x) {
            $(r).appendTo(Paginator.opts.target);
            Paginator.initPages();
            Paginator.scanLandmarks();
        });
    },

    nav: function(loc, evt) {
        var offset = parseInt(loc.href.match(/offset=(\d+)/)[1]),
            page = (offset + Paginator.opts.delta) / Paginator.opts.delta;

        if (!un.pgFixedLoaded[page]) return true;
        else {
            $(window).scrollTop($('li[data-paginator="page-'+ page +'"]').offset().top - 80);
            return false;
        }
    }
};

try {stmgr.loaded('pagination.js');}catch(e){}