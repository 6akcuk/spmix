function getUUID() {
    return A.uuid++;
}

/** VK.com (common.js) Useful functions */
Function.prototype.pbind = function() {
    var args = Array.prototype.slice.call(arguments);
    args.unshift(window);
    return this.bind.apply(this, args);
};
Function.prototype.bind = function() {
    var func = this, args = Array.prototype.slice.call(arguments);
    var obj = args.shift();
    return function() {
        var curArgs = Array.prototype.slice.call(arguments);
        return func.apply(obj, args.concat(curArgs));
    }
}

/* Report Window */
var report_window = {
    margin: 20,

    create: function(over, pos, text) {
        var over = $(over),
            id = 'rw-'+ over.attr('id'),
            left = 0,
            top = 0;

        $('#'+ id).remove();

        var rw = $('<div id="'+ id +'" class="report_window">' +
            '<a class="close iconify_x_a"></a>'
            + text +'' +
            '</div>').appendTo('body');

        rw.find('a.close').click(report_window.remove);

        if (pos == 'left') {
            top = over.offset().top;
            left = over.offset().left - report_window.margin - rw.outerWidth();
        }
        else if (pos == 'right') {
            top = over.offset().top;
            left = over.offset().left + report_window.margin + rw.outerWidth();
        }
        else if (pos == 'top') {
            top = over.offset().top - report_window.margin - rw.outerHeight();
            left = over.offset().left + (over.outerWidth() / 2) - (rw.outerWidth() / 2);
        }
        else if (pos == 'bottom') {
            top = over.offset().top + over.outerHeight() + report_window.margin;
            left = over.offset().left + (over.outerWidth() / 2) - (rw.outerWidth() / 2);
        }

        rw.show().css({
            left: left,
            top: top
        })
    },

    remove: function() {
        $(this).parent().remove();
    }
};

/* Tooltip */
var Tooltip = {
    cur: null,
    d: null,

    show: function(el) {
        if ($(el).attr('title') == '') return;

        var c = $(el),
            t = $('<div/>').addClass('tooltip')
            .html('<div class="tt_bg"></div><div class="tt_text">'+ c.attr('title') + '</div>')
            .appendTo('body'),
            b = t.find('div.tt_bg');
        Tooltip.cur = t;
        Tooltip.d = c;
        c.attr('title', '');
        t.css({
            top: parseInt(c.offset().top - 10 - t.outerHeight()),
            left: parseInt(c.offset().left + (c.outerWidth() / 2) - (t.outerWidth() / 2))
        });
        b.css({
            width: t.find('div.tt_text').outerWidth(),
            height: t.find('div.tt_text').outerHeight()
        });
    },
    hide: function() {
        if (Tooltip.cur) {
            Tooltip.d.attr('title', Tooltip.cur.text());
            Tooltip.cur.remove();
        }
        Tooltip.cur = null;
    },
    init: function() {
        $('div.tooltip').remove();
        $('.tt').mouseenter(function() {
                Tooltip.show(this)
            }).mouseleave(function() {
                Tooltip.hide();
            });
    }
};

/* Minimal Save Informer */
var msi = {
    show: function(msg) {
        var _popup = $('<div class="msi">'+ msg +'</div>').appendTo('body');
        _popup.css({
            left: ($('body').width() - _popup.outerWidth()) / 2
        });
        setTimeout(function() {
            _popup.remove();
        }, 3000);
    }
};

/* AJAX Exception Window */
var ajex = {
    show: function(msg) {
        var _lAjx = $('<div class="ajex">'+ msg +'</div>').appendTo('body');
        setTimeout(function() {
            _lAjx.remove();
        }, 5000);
    }
};

/* On Line Context Menu */
var olcm = {
    _e: null,

    setItem: function(el, item, fn) {
        el = $(el);
        el.html(item);
        olcm.hide(el);
        $('body').unbind('click', olcm.hide);

        if ($.isFunction(fn)) fn(el, item);
    },
    show: function(el, items, fn) {
        var e = $(el),
            c = $('<div class="olcm" onclick="event.cancelBubble=true"><h4></h4><ul></ul></div>').insertAfter(e),
            h = c.children('h4'),
            u = c.children('ul');

        olcm._e = e;
        h.html(e.text());
        $.each(items, function(i,item) {
            var li = $('<li><a>'+ item +'</a></li>').appendTo(u),
                a = li.children('a');

            a.click(function() {
                olcm.setItem(el, item, fn);
            });
        });

        setTimeout(function() {
            $('body').one('click', olcm.hide);
        }, 1);
    },
    hide: function() {
        olcm._e.next().remove();
    }
};

/* Horizontal Menu With Slides Submenu */
var hmss = {
    showtimer: null,
    hidetimer: null,
    firstRun: true,
    active: false,
    menuFocused: false,
    element: null,

    isSame: function(el) {
        return (hmss.getElementName(el) == hmss.getElementName(hmss.element)) ? true : false;
    },

    getElementName: function(el) {
        return $(el).children('a').text();
    },

    mouseenter: function() {
        var self = this,
            prev = hmss.element;

        if (hmss.firstRun) {
            if (!$(self).has('ul').length) return;

            hmss.showtimer = setTimeout(function() {
                hmss.showtimer = null;
                hmss.firstRun = false;

                var sm = $(self).find('ul'),
                    inc = parseInt(sm.css('paddingTop')) * 2 + sm.height();

                sm.show();
                $(self).parent().parent().animate({
                    height: '+='+ inc +'px'
                }, 100);
                $(self).children('a').addClass('opened');
            }, 200);
        }
        else {
            if (!$(self).has('ul').length) return;

            clearTimeout(hmss.hidetimer);
            if (hmss.isSame(self)) return;

            $(prev).find('ul').hide();
            $(self).find('ul').show();
            $(self).children('a').addClass('opened');
        }
    },

    mouseleave: function() {
        var self = this;

        clearTimeout(hmss.showtimer);
        if (!hmss.firstRun) {
            $(self).children('a').removeClass('opened');
            hmss.hidetimer = setTimeout(function() {
                hmss.hidetimer = null;
                hmss.firstRun = true;

                var sm = $(self).find('ul'),
                    inc = parseInt(sm.css('paddingTop')) * 2 + sm.height();

                sm.hide();
                $(self).parent().parent().animate({
                    height: '-='+ inc +'px'
                }, 100);
            }, 600);
        }
    }
};

/* Input Placeholder */
var input_ph = {
    focus: function() {
        $(this).next().hide();
    },
    blur: function() {
        if ($.trim($(this).val()) == '') $(this).next().show();
    },
    subscribe: function() {
        $(this).next().css({
            top: parseInt($(this).css('paddingTop')) / 2,
            left: $(this).css('paddingLeft'),
            display: (($(this).val() == '') ? 'block' : 'none')
        }).click(function() {
            $(this).prev().focus();
        });
    },
    contentChanged: function() {
        $('#content span.input_placeholder input, #content span.input_placeholder textarea')
            .focus(input_ph.focus)
            .blur(input_ph.blur)
            .each(input_ph.subscribe);
    }
};

$.fn.inputPlaceholder = function() {
    this.each(function()
    {
        $(this).focus(function(e) {
            $(this).next().hide();
        }).blur(function() {
            if ($.trim($(this).val()) == '') $(this).next().show();
        });
        $(this).next().css({
            top: parseInt($(this).css('paddingTop')) / 2,
            left: $(this).css('paddingLeft'),
            display: (($(this).val() == '') ? 'block' : 'none')
        }).click(function() {
            $(this).prev().focus();
        });
    });
}

/* Select menu */
var dropdown = {

}

$.fn.dropdown = function() {

    this.each(function()
    {
        // Creating new element
        if (!$(this).hasClass('dropdown')) {

        }

        var c = $(this),
            h = c.children('input[type="hidden"]'),
            t = c.children('span.text'),
            i = c.children('input[type="text"]'),
            m = c.children('ul');

        if (!$(this).hasClass('clearfix')) $(this).addClass('clearfix');
        $(this).click(function(ev) {
            ev.stopPropagation();

            m.show().css({
                width: c.width() - 1,
                left: 0,// c.offset().left,
                top: t.outerHeight() //c.offset().top + c.outerHeight()
            });

            $('body').bind('click', hideDDMenu);
        });

        function hideDDMenu() {
            m.hide();
            $('body').unbind('click', hideDDMenu);
        }

        m.find('a').click(function(ev) {
            ev.stopPropagation();
            t.html($(this).text());
            h.val($(this).attr('data-value'));
            hideDDMenu();

            //if (c.attr('onchange'))
            h.change();// eval(c.attr('onchange'));
        });
    });
}

/* Calendar */
$.fn.calendar = function() {
    this.each(function()
    {
        if (!$(this).hasClass('input_calendar'))
            $(this).addClass('input_calendar');

        /** (C)ontainer, (H)eader, (T)itle, (W)eekdays, (L)ist, (P)revious, (N)ext */
        var over = $(this),
            c, h, t, w, l, p, n;

        var _c = Calendar = {
            months: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
            lmonths: ['Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря'],
            curMY: null,
            default: '',
            isFirst: true,

            prev: function() {
                var month = _c.curMY[0],
                    year = _c.curMY[1];

                if (month == 0) {
                    month = 11;
                    year--;
                }
                else month--;

                _c.curMY = [month, year];

                _c.renderTitle();
                _c.renderDays();
                _c.reposite();
            },
            next: function() {
                var month = _c.curMY[0],
                    year = _c.curMY[1];

                if (month == 11) {
                    month = 0;
                    year++;
                }
                else month++;

                _c.curMY = [month, year];

                _c.renderTitle();
                _c.renderDays();
                _c.reposite();
            },

            renderTitle: function() {
                t.html(_c.months[_c.curMY[0]] + ' '+ _c.curMY[1]);
            },

            renderDays: function() {
                var curDate = new Date(_c.curMY[1], _c.curMY[0]),
                    prevMonth = new Date(_c.curMY[1], _c.curMY[0], 1),
                    daysNum = new Date(_c.curMY[1], _c.curMY[0]+1, 0).getDate(),
                    firstDay = prevMonth.getDay(),
                    date = 1;

                l.html('');
                firstDay = (firstDay == 0) ? 6 : firstDay - 1;
                for(var d = 0; d < 7; d++) {
                    if (d < firstDay) $('<li><em>&nbsp;</em></li>').appendTo(l);
                    else {
                        _c.renderDay(date);
                        date++;
                    }
                }
                for(;date <= daysNum; date++) {
                    _c.renderDay(date);
                }
            },

            renderDay: function(date) {
                var $li = $('<li><a>'+ date +'</a></li>').appendTo(l),
                    $a  = $li.children('a');
                $a.click(_c.onClickDate);
            },

            onClickDate: function() {
                var date = parseInt($(this).text()),
                    month = _c.curMY[0],
                    year = _c.curMY[1];

                over.attr('title', _c.default);
                over.children('input').val(year +'-'+ (month+1) +'-'+ date);
                over.children('input').blur(); // позволяет вызывать пользовательские события
                over.children('span').html(date + ' '+ _c.lmonths[month] + ' '+ year);
                over.children('em').show();
                _c.hide();
            },

            show: function() {
                c = $('<div/>')
                    .attr({class: 'calendar'})
                    .click(function(ev){ev.stopPropagation()})
                    .appendTo('body')
                    .html('<div class="header clearfix">\
                    <a class="left iconify_prev_a"></a>\
                    <span class="left"></span>\
                    <a class="left iconify_next_a"></a>\
                </div>\
                <ul class="weekdays list"><li>Пн</li><li>Вт</li><li>Ср</li><li>Чт</li><li>Пт</li><li>Сб</li><li>Вс</li></ul>\
                <ul class="list"></ul>\
                ');
                h = c.find('div.header');
                t = h.find('span');
                w = c.find('ul.weekdays');
                l = w.next();

                h.find('a.iconify_prev_a').click(_c.prev),
                h.find('a.iconify_next_a').click(_c.next);

                if (!_c.curMY) _c.curMY = [new Date().getMonth(), new Date().getFullYear()];
                _c.renderTitle();
                _c.renderDays();

                _c.reposite();

                setTimeout(function() {
                    $('body').bind('click', _c.hide);
                }, 100);
            },
            hide: function() {
                $('body').unbind('click', _c.hide);
                c.remove();
            },
            clear: function(e) {
                e.stopPropagation();
                over.children('em').hide();
                over.children('span').html(_c.default);
                over.attr('title', '');
                over.children('input').val('');
                over.children('input').blur(); // позволяет вызывать пользовательские события
            },
            checkPreinit: function() {
                var dt = over.find('input').val(),
                    data = [];

                if (dt != '') {
                    data = dt.split('-');
                    var date = data[2], month = parseInt(data[1]) - 1, year = parseInt(data[0]);
                    _c.curMY = [month, year];
                    over.children('em').show();
                    over.children('span').html(date + ' '+ _c.lmonths[month] + ' '+ year);
                    over.attr('title', _c.default);
                }
            },

            reposite: function() {
                c.css({
                    left: over.offset().left + over.outerWidth() / 2 - c.outerWidth() / 2,
                    top: over.offset().top - 10 - c.outerHeight()
                });
            }
        }

        over.click(Calendar.show);
        over.children('em').click(Calendar.clear);
        Calendar.default = over.find('span').text();

        if (Calendar.isFirst) {
            Calendar.checkPreinit();
            Calendar.isFirst = false;
        }
    });
};

/* Text Editor */
$.fn.editor = function(type, url, params)
{
    this.each(function()
    {
        var $this = $(this);
        $this.hide();

        if (type == 'simple')
        {
            var _e = {
                cont: null,
                init: function()
                {
                    _e.cont = $('<div/>').attr({class: 'editor_simple'}).insertBefore($this);
                    _e.textarea = $('<textarea/>').attr({id: 'edtext_'+ getUUID()}).appendTo(_e.cont);

                    ajax.post(url, params, _e.onInitDone);
                },

                onInitDone: function(r)
                {
                    _e.textarea.css({width: $this.parent().innerWidth() - 20})
                        .val(r.text).focus().blur(_e.onBlurTextarea);

                    _e.textarea.autosize({width: $this.parent().innerWidth() - 20});
                    _e.textarea.keyup();
                },
                onUpdateDone: function(r) {
                    _e.cont.remove();
                    $this.show().html((r.text) ? r.text : 'Добавить описание');
                    msi.show(r.msg);
                },
                onBlurTextarea: function()
                {
                    params = $.extend(params, {text: _e.textarea.val()});
                    ajax.post(url, params, _e.onUpdateDone);
                }
            };

            _e.init();
        }
    });
};

/* Autosize */
/* Source from project: Petsface.ru */
$.fn.autosize = function(options) {
    this.each(function()
    {
        var settings = {
                minheight: 30,
                maxheight: 160,
                lineHeight: '16px',
                padding: 5
            },
            timeout = null,
            enabled = true;

        var ps = $.extend(settings, options),
            $this = $(this),
            $as = $('<div/>')
                .attr({
                    class: 'autosize'
                })
                .css({
                    width: $this.width(),
                    fontSize: $this.css('fontSize'),
                    padding: (ps.padding*2),
                    lineHeight: ps.lineHeight
                })
                .appendTo('#utils');

        oldValue = $this.val();
        if ($this.attr('maxheight')) ps.maxheight = parseInt($this.attr('maxheight'));
        ps.id = '#'+ $this.attr('id');

        function check(event) {
            var html = $this.val().replace(/\n/g, "<br>");
            if(event.type != 'keyup') {
                if(event.keyCode == 13 && !event.ctrlKey && !event.altKey) {
                    html += '\n';
                }
            }
            if(html == oldValue) return;
            oldValue = html;

            $as.html(html);

            if(ps.maxheight) $this.css('height', Math.max(ps.minheight, Math.min( ($as.outerHeight() + (ps.padding+2)), ps.maxheight) ));
            else $this.css('height', Math.max(ps.minheight, ($as.outerHeight() + (ps.padding+2)) ));

            // покажем scroll при превышении высоты
            if(ps.maxheight) {
                if($as.outerHeight() > ps.maxheight) $this.css('overflow', 'auto');
                else $this.css('overflow', 'hidden');
            }
        }

        function destroy() {
            $as.remove();
            $this.unbind('keyup').unbind('keydown').unbind('keypress');

            enabled = false;
        }

        $this.bind('keyup keydown keypress', check);
    });
}

/* Tabs */
$.fn.tabs = function()
{
    this.each(function()
    {
        var $this = $(this);
        $(this).children('a').click(function()
        {
            var $link = $($this.attr('data-link'));
            $this.children('a').removeClass('selected');
            $link.children('div').hide();
            $link.children($(this).attr('target')).show();
            $(this).addClass('selected');
        });
    });
}

/* Boxes */
var _box_guid = 0;
var _bq = {
    _boxes: [],
    curBox: 0
};

function Box(opts, dark) {
    var defaults = {
        title: false,
        width: 410,
        height: 'auto',
        progress: false,
        hideButtons: false,
        hideOnBGClick: false,
        onShow: false,
        onHide: false
    };

    opts = $.extend(defaults, opts);

    var boxContainer, boxBG, boxLayout;
    var boxTitleWrap, boxTitle, boxCloseButton, boxBody;
    var guid = _box_guid++, visible = false;

    if (!opts.progress) opts.progress = 'box_progress'+ guid;

    var controlsStyle = (opts.hideButtons) ? ' style="display:none"' : '';
    boxContainer = $('<div/>').addClass('popup_box_container');
    if (dark) boxContainer.addClass('box_dark');
    boxContainer.html('\
<div class="box_layout" onclick="_bq.skip=true;">\
<div class="box_title_wrap"><div class="box_x_button">'+(dark ? 'Закрыть' : '')+'</div><div class="box_title"></div></div>\
<div class="box_body" style="' + options.bodyStyle + '"></div>\
<div class="box_controls_wrap"' + controlsStyle + '><div class="box_controls">\
<table cellspacing="0" cellpadding="0" class="fl_r"><tr></tr></table>\
<div class="progress" id="' + options.progress + '"></div>\
<div class="box_controls_text"></div>\
</div></div>\
</div>');
    boxContainer.hide();
}

/* Simple Field Add/Remove */
var sfar = {
    add: function(plus) {
        var $p = $(plus).parent(),
            $c = $($p.clone()).insertAfter($p);
        $c.find('input').val('').inputPlaceholder();
        $c.children('a.iconify_x_a').show();
    },

    del: function(x) {
        $(x).parent().remove();
    }
};
/* Simple Block Add/Remove */
var sbar = {
    _i: 1,
    add: function(b) {
        var $c = $($('[rel="sbar"]')[0]).clone(),
            i = sbar._i++;
        $c.insertBefore($(b).parent());
        $c.find('[sbar="sub"]').each(function(i, e) {
            if (i > 0) $(e).remove();
        });
        $c.attr('id', 'block'+ i);
        $c.find('input').each(function(idx, inp) {
            $(inp).attr('name', $(inp).attr('name').replace(/\d+/g, i));
            $(inp).val('').inputPlaceholder();
        });
        $c.find('div.row:first-child a.iconify_x_a').show();
    },

    del: function(x) {
        $(x).parent().parent().remove();
    }
}

/* Form Easy Control */
var FormMgr = {
    submit: function(el, pos, onDone) {
        var $form = $(el),
            url = $form.attr('action');

        if (!pos) pos = 'right';

        $form.find('input').removeClass('error');

        ajax.post(url, $form.serialize(), function(response) {
            if (response.success) {
                if ($.isFunction(onDone)) onDone(response);
                else {
                    var scs = $('<div/>').addClass('success').insertAfter($form.children('h2'));
                    scs.html(response.message);
                    if (response.msg) msi.show(response.msg);
                    if (response.url) nav.go(response.url);
                }
            }
            else {
                var string = [];
                $.each(response, function(i, v) {
                    $('#'+ i).addClass('error');
                    if ($.isArray(v)) {
                        $.each(v, function(i2, v2) {
                            string[string.length] = v2;
                        })
                    }
                    else string[string.length] = v;
                });

                report_window.create($form, pos, string.join('<br/>'));
            }
        });

        return false;
    }
};

/* Static Manager */
var stmgr = {
    _css: new RegExp('.css', 'i'),
    _js: new RegExp('.js', 'i'),
    _waiters: [],
    _iv: null,
    _ivWork: false,
    _failover: 10,

    init: function() {
        stmgr._ivWork = true;
        stmgr._iv = setInterval(stmgr.wait, 500);
    },

    wait: function() {
        if (!stmgr._waiters.length) {
            stmgr._waiters = [];
            stmgr._ivWork = false;
            clearInterval(stmgr._iv);
            return;
        }

        $.each(stmgr._waiters, function(i, waiter) {
            if (!waiter) return;

            if (stmgr._css.test(waiter[0])) {
                //alert(waiter.n);
                //alert($('#'+ waiter.n +'_css').css('display'));

                if ($('#'+ waiter[0].replace(/.css/ig, '') +'_css').css('display') == 'none') {
                    stmgr.loaded(waiter[0]);
                    stmgr._waiters.splice(i, 1);
                }
                else stmgr._waiters[i][1]++;

                if (stmgr._waiters[i][1] >= stmgr._failover) {
                    stmgr._waiters.splice(i, 1);
                    ajex.show('Не удалось загрузить '+ waiter[0] +'');
                }
            }
            else if (stmgr._js.test(waiter[0])) {
                if (staticFiles[waiter[0]].l == 1) stmgr._waiters.splice(i, 1);
                else stmgr._waiters[i][1]++;

                if (stmgr._waiters[i][1] >= stmgr._failover) {
                    stmgr._waiters.splice(i, 1);
                    ajex.show('Не удалось загрузить '+ waiter[0] +'');
                }
            }
        });
    },

    add: function(url, name, version) {
        if (staticFiles[name] && staticFiles[name].v == parseInt(version) && staticFiles[name].l) {
            logger.add(name +' уже загружен');
            return;
        }

        logger.add('Загрузка '+ name +' версии '+ version);

        staticFiles[name] = {v: parseInt(version), l: 0};
        stmgr._waiters.push([name, 0]);
        if (!stmgr._ivWork) stmgr.init();

        logger.add(name+ ' в очереди ожидания');

        if (stmgr._css.test(name)) {
            logger.add(name+ ' прошел тест на CSS');

            $('<div/>')
                .attr('id', name.replace(/\.css/ig, '') + '_css')
                .appendTo('#utils');

            $('<link/>')
                .attr({
                    type: 'text/css',
                    rel: 'stylesheet',
                    href: url
                })
                .appendTo('head');
        }
        else if (stmgr._js.test(name)) {
            logger.add(name+ ' прошел тест на JS');
            //alert('Adding '+ name +' '+ url);

            $('<script/>')
                .attr({
                    type: 'text/javascript',
                    src: url
                })
                .appendTo('head');
        }
    },

    loaded: function(name) {
        staticFiles[name]['l'] = 1;
    }
};

/* Logger */
var logger = {
    _vault: [],

    add: function(msg) {
        logger._vault[logger._vault.length] = msg;
    },

    showAll: function() {
        ajex.show(logger._vault.join('<br/>'));
        logger._vault = [];
    }
};

/* Upload */
var Upload = {
    map: {},

    assign: function(map) {
        $.extend(Upload.map, map || {});
    },
    cancel: function(id) {
        Upload.showInput(id);
        Upload.initFile(id);
    },
    initFile: function(id, customChange) {
        if (!$('#file_button_'+ id +' div.filebutton input[type="file"]').size()) {
            $('<input/>')
                .attr({type: 'file', name: 'photo', id: 'file_upload_'+ id})
                .change(function(){
                    if (customChange) customChange();
                    else Upload.onStart(id)
                })
                .prependTo('#file_button_'+ id +' div.filebutton');
        }
    },
    deleteFile: function(id) {
        var $button = $('#file_button_'+ id);
        $button.prev().val('');
        $button.find('div.filedata img').remove();
        Upload.showInput(id);
        Upload.initFile(id);
    },
    onDOMReady: function($button) {
        var $button = $($button),
            id = parseInt($button.attr('id').replace('file_button_', '')),
            filedata = $.parseJSON($button.prev().val());

        if (filedata && (filedata.x || filedata.doc)) {
            Upload.showData(id);
            Upload.renderData(id, filedata);
        }
        else Upload.showInput(id);
    },
    showProgress: function(id) {
        var $button = $('#file_button_'+ id);
        $button.children('div.fileprogress').show();
        $button.children('div.filedata').hide();
        $button.children('div.filebutton').hide();
    },
    showInput: function(id) {
        var $button = $('#file_button_'+ id);
        $button.children('div.fileprogress').hide();
        $button.children('div.filedata').hide();
        $button.children('div.filebutton').show();
    },
    showData: function(id) {
        var $button = $('#file_button_'+ id);
        $button.children('div.fileprogress').hide();
        $button.children('div.filedata').show();
        $button.children('div.filebutton').hide();
    },
    renderData: function(id, json) {
        if (json['x']) {
            var size = $('#file_button_'+ id).attr('data-image');
            $('<img/>').attr({src: 'http://cs'+ json[size][2] +'.spmix.ru/'+ json[size][0] + '/'+ json[size][1], alt: ''})
                .prependTo('#file_button_'+ id +' div.filedata');
        }
        else if (json['doc']) {

        }
    },
    onStart: function(id) {
        var $div = $('<div/>').attr({id: 'file_ctrl_'+ id}).addClass('filectrl').appendTo('#utils'),
            $iframe = $('<iframe/>').attr({
                name: 'upload_iframe_'+ id
            }).appendTo($div),
            $form = $('<form/>').attr({
                id: 'file_form_'+ id,
                action: Upload.map.action,
                enctype: 'multipart/form-data',
                method: 'post',
                target: 'upload_iframe_'+ id
            }).appendTo($div),
            $button = $('#file_button_'+ id);

        $('<input/>').attr({type: 'hidden', name: 'form_id', value: id}).appendTo($form);

        cur['fileDone'+ id] = Upload.onDone.pbind(id);
        cur['fileFailed'+ id] = Upload.onFail.pbind(id);

        $form.append($('#file_upload_'+ id));
        $form.submit();
        Upload.showProgress(id);
    },
    onDone: function(id, filedata) {
        $('#file_button_'+ id).prev('input').val(filedata);
        $('#file_ctrl_'+ id).remove();
        Upload.showData(id);
        Upload.renderData(id, $.parseJSON(filedata));
        if (cur['fileDoneCustom'+ id]) cur['fileDoneCustom'+ id](filedata);
    },
    onFail: function(id, message) {
        $('#file_ctrl_'+ id).remove();
        Upload.showInput(id);
        Upload.initFile(id);
        report_window.create('#file_button_'+ id, 'left', message);
    }
};

/* Search Filters */
$.fn.filters = function() {
    this.each(function()
    {
        var $el = $(this).children();

        if ($el.hasClass('input_placeholder')) {
            var $this = $el.find('input');
            $this.blur(function() {
                nav.go('?'+ $this.attr('name') +'='+ $this.val(), event, {search: true, revoke: ($this.val() == '')});
            }).keypress(function(ev) {
                if (ev.keyCode == 13) nav.go('?'+ $this.attr('name') +'='+ $this.val(), event, {search: true});
            });
        }
        else if ($el.hasClass('input_calendar')) {
            var $this = $el.find('input');
            $this.blur(function() {
                nav.go('?'+ $this.attr('name') +'='+ $this.val(), event, {search: true, revoke: ($this.val() == '')});
            });
        }
        else if ($el.hasClass('dropdown')) {
            var $this = $el.find('input');
            $this.change(function() {
                nav.go('?'+ $this.attr('name') +'='+ $this.val(), event, {search: true, revoke: ($this.val() == '')});
            });
        }
    });
}

/* Navigation Object */
/* Great respect for VK.com developers */
var nav = {
    _ivCheck: null,
    _tmPage: null,
    curLoc: false,
    objLoc: {},
    request: null,

    q2obj: function(qa) {
        if (!qa) return {};
        var query = {} , dec = function(str) {
            return decodeURIComponent(str);
        };
        qa = qa.split('&');
        $.each(qa, function(i, a) {
            var t = a.split('=');
            if (t[0]) {
                var v = dec(t[1] + '');
                if (t[0].substr(t.length - 2) == '[]') {
                    var k = dec(t[0].substr(0, t.length - 2));
                    if (!query[k]) {
                        query[k] = [];
                    }
                    query[k].push(v);
                }
                else if (t[0].match(/(\[|\])/g)) {
                    var k = t[0].split('['),
                        n = dec(k[0]),
                        m = dec(k[1].replace(']', ''));
                    if (!query[n]) {
                        query[n] = {};
                    }
                    query[n][m] = v;
                } else {
                    query[dec(t[0])] = v;
                }
            }
        });
        return query;
    },
    obj2q: function(qa) {
        var query = [], enc = function(str) {
            return encodeURIComponent(str);
        };

        for (var key in qa) {
            if (qa[key] == null || $.isFunction(qa[key])) continue;
            if ($.isArray(qa[key])) {
                for (var i = 0, c = 0, l = qa[key].length; i < l; ++i) {
                    if (qa[key][i] == null || $.isFunction(qa[key][i])) {
                        continue;
                    }
                    query.push(enc(key) + '[' + c + ']=' + enc(qa[key][i]));
                    ++c;
                }
            }
            else if ($.isPlainObject(qa[key])) {
                for(var i in qa[key]) {
                    query.push(enc(key) + '['+ enc(i) +']=' + enc(qa[key][i]));
                }
            } else {
                query.push(enc(key) + '=' + enc(qa[key]));
            }
        }
        query.sort();
        return query.join('&');
    },
    revoke: function(qa, str) {
        qa = qa.split('&');
        $.each(qa, function(i, a) {
            if (!a) return;
            var t = a.split('='),
                s = str.split('=');

            if (t[0] == s[0]) qa.splice(i, 1);
        });
        return qa.join('&');
    },
    query: function(loc, opts) {
        var a = loc.split('?'),
            curLoc = nav.curLoc.split('?'),
            obj = nav.q2obj(a[1]),
            curObj = nav.q2obj(curLoc[1]);

        if (a[1]) {
            if (a[0] == '') a[0] = curLoc[0];
            if (!opts.search) nav.objLoc = {};
            $.extend(true, nav.objLoc, (!opts.search) ? {} : curObj, obj);
            q = nav.obj2q(nav.objLoc);
            if (opts.revoke) {
                q = nav.revoke(q, a[1]);
                nav.objLoc = nav.q2obj(q);
            }
            a[1] = q;
            return a.join('?');
        }
        else {
            nav.objLoc = {};
            return loc;
        }
    },
    go: function(loc, event, _opts) {
        if (loc.tagName && loc.tagName.toLowerCase() == 'a' && loc.href) {
            if (loc.target == '_blank') {
                return;
            }
            loc = loc.href;
        }

        loc = loc.replace(new RegExp('(http://'+ A.host + ')', 'i'), '');

        if (loc) {
            var opts = {
                same: false,
                container: '#content',
                revoke: false
            };
            opts = $.extend(opts, _opts);

            loc = nav.query(loc, opts);

            if (opts.paginator) {
                todo = Paginator.nav(loc.match(/offset=(\d+)/)[1]);
                if (!todo) return false;
            }

            var where = loc.split('?');
            if (where[1]) {
                where.url = loc;
                where.params = where[1];
            }
            else {
                where.url = loc;
                where.params = '';
            }

            if (nav.request) {
                clearTimeout(nav._tmPage);
                nav.request.abort();
            }
            nav.request = $.ajax({
                type: 'POST',
                dataType: 'json',
                url: where.url,
                data: where.params
            });
            $('body').addClass('progress');

            nav.request.done(function(response, status, xhr) {
                if (response.guest && A.user_id > 0) {
                    location.href = A.host;
                    return;
                }

                nav.curLoc = loc;
                location.hash = loc;

                $('body').removeClass('progress');
                clearTimeout(nav._tmPage);
                nav.request = null;

                $.each(response.static, function(i, st) {
                    stmgr.add(st[0], st[1], st[2])
                });

                // Page Counters
                if (response.counters['friends']) {
                    var iCnt = parseInt(response.counters['friends']),
                        $friends_cnt = $('#friends_link').find('a.right');
                    if ($friends_cnt.size()) {
                        (iCnt > 0) ? $friends_cnt.html('+'+ iCnt) : $friends_cnt.remove();
                    }
                    else {
                        if (iCnt > 0)
                            $('<a href="/friends?section=requests" onclick="return nav.go(this, event)" class="right lm-counter">+'+ iCnt +'</a>').appendTo('#friends_link');
                    }
                }

                if (response.counters['pm']) {
                    var iCnt = parseInt(response.counters['pm']),
                        $pm_cnt = $('#pm_link').find('a.right');
                    if ($pm_cnt.size()) {
                        (iCnt > 0) ? $pm_cnt.html('+'+ iCnt) : $pm_cnt.remove();
                    }
                    else {
                        if (iCnt > 0)
                            $('<a href="/im" onclick="return nav.go(this, event)" class="right lm-counter">+'+ iCnt +'</a>').appendTo('#pm_link');
                    }
                }

                //logger.showAll();

                $('title').html(response.title);
                $(opts.container).html(response.html);
                if (response.widescreen) {
                    $('#body > div.wrap > div.maincolumns > div.smallcolumn').hide();
                    $('#content').removeClass('largecolumn');
                }
                else {
                    if (!$('#content').hasClass('wrap') && !$('#content').hasClass('largecolumn')) {
                        $('#body > div.wrap > div.maincolumns > div.smallcolumn').show();
                        $('#content').addClass('largecolumn');
                    }
                }
                $('#content').trigger('contentChanged');
            });
            nav.request.fail(function(xhr, textStatus, errorThrown) {
                if (xhr.response && xhr.response.guest && A.user_id > 0) {
                    location.href = A.host;
                    return;
                }

                $('body').removeClass('progress');
                clearTimeout(nav._tmPage);
                nav.request = null;

                switch (xhr.status) {
                    case 403:

                        break;
                    case 404:
                        ajex.show('Страница не найдена');
                        break;
                    default:
                        ajex.show('Ошибка связи с сервером. Перезагрузите страницу, нажав <b>F5</b>. '+ xhr.responseText);
                }
            });

            clearTimeout(nav._tmPage);
            nav._tmPage = setTimeout(function() {
                //$.jGrowl('Соединение с сервером разорвано, перезагрузите страницу', {theme: 'danger'});
                ajex.show('Соединение с сервером разорвано, перезагрузите страницу');
                $('body').removeClass('progress');
                nav.request.abort();
                nav.request = null;
            }, 5000);
        }
        return false;
    },

    reload: function() {
        nav.curLoc = null;
    },

    check: function() {
        var loc = location.hash.replace(/^#/i, '');
        if (loc == nav.curLoc) return;
        if (loc != '' && !loc.match(/^\//i)) return;
        if (loc == '') return;

        nav.curLoc = loc;
        nav.go(loc);
    },

    init: function() {
        nav.curLoc = location.pathname;//.replace(/^\/{1}/i, '');
        clearInterval(nav._ivCheck);
        nav._ivCheck = setInterval(nav.check, 200);
    }
};

var ajax = {
    STATUS_OK: 1,
    STATUS_ERR: 0,
    STATUS_EXCEPTION: -1,

    post: function(url, params, onDone, onFail) {
        var request = $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            data: params
        });
        $('body').addClass('progress');

        request.done(function(response, status, xhr) {
            $('body').removeClass('progress');

            if ($.isFunction(onDone)) onDone(response);
        });
        request.fail(function(xhr, textStatus) {
            $('body').removeClass('progress');
            ajex.show('Ошибка связи с сервером: '+ xhr.responseText);

            if ($.isFunction(onFail)) onFail(xhr);
        });
    },

    script: function(url, options) {
        options = $.extend(options || {}, {
            dataType: "script",
            cache: true,
            url: url
        });

        return jQuery.ajax(options);
    }
};
nav.init();

var cur = [];
window['cur'] = cur;

/* DOM ready */
$().ready(function() {
    $win = $(window);

    $(window).resize(function() {
        $('#stl_left').css({
            width: $('div.main > div.wrapper').offset().left,
            height: $(window).height()
        });

        if (typeof Paginator !== "undefined") Paginator.onWindowResize();
    }).scroll(function() {
        if ($win.scrollTop() > 50) {
            $('#stl_left').show();

            if ($win.scrollTop() > $('div.maincolumns > div.smallcolumn').offset().top + $('div.maincolumns > div.smallcolumn').outerHeight()) {
                if (!A.stlLeftWide) {
                    A.stlLeftWide = true;
                    $('#stl_left').css({
                        width: $('div.maincolumns > div.largecolumn').offset().left
                    });
                }
            }
            else {
                if (A.stlLeftWide) {
                    A.stlLeftWide = false;
                    $('#stl_left').css({
                        width: $('div.main > div.wrapper').offset().left
                    });
                }
            }
        }
        else $('#stl_left').hide();
    });

    // up
    $('#stl_left').mouseenter(function() {
        $(this).children('#stl_bg').addClass('hover');
    }).mouseleave(function() {
        $(this).children('#stl_bg').removeClass('hover');
    }).click(function() {
       $win.scrollTop(0);
    });

    // slides menus
    $('ul.hmenu_slidesubmenu > li')
        .mouseenter(hmss.mouseenter)
        .mouseleave(hmss.mouseleave);

    // input placeholder
    $('span.input_placeholder input, span.input_placeholder textarea').inputPlaceholder();

    $('div.dropdown').dropdown();
    $('a.input_calendar').calendar();
    Tooltip.init();

    // file uploader
    $('div.fileupload').each(function()
    {
        Upload.onDOMReady(this);
    });

    $('div.tabs').tabs();
    $('.smarttext textarea').autosize();
    $('[rel="filters"]').filters();

    // content updates with ajax
    $('#content').on('contentChanged', function() {
        $('#content span.input_placeholder input, #content span.input_placeholder textarea').inputPlaceholder();
        $('div.dropdown').dropdown();
        Tooltip.init();
        $('a.input_calendar').calendar();
        $('div.tabs').tabs();

        $('div.autosize').remove();
        $('.smarttext textarea').autosize();

        $('div.fileupload').each(function()
        {
            Upload.onDOMReady(this);
        });

        $('[rel="filters"]').filters();
        $(window).resize();
    });

    $(window).resize();
});

try {stmgr.loaded('activehtmlelements.js');}catch(e){}