$(function() {
    var itemTotal = $('.main div[item]').length;
    var itemCount = 0;
    var empty = true;

    var loadElements = function(item, classId = null) {
        $.getJSON('/moonlight/elements/list', {
            item: item,
            classId: classId
        }, function(data) {
            if (data.html && data.html.length) {
                $('.main div[item="' + item + '"]').hide().html(data.html).fadeIn(200);

                empty = false;
            }

            itemCount++;

            if (itemCount == itemTotal && empty) {
                $('div.empty').show();
            }
        }).fail(function() {
            $.alertDefaultError();
        });
    };

    var getElements = function(item, classId = null, page = 1) {
        $.getJSON('/moonlight/elements/list', {
            item: item,
            classId: classId,
            page: page
        }, function(data) {
            if (data.html && data.html.length) {
                $('.main div[item="' + item + '"]').html(data.html);
            }
        }).fail(function() {
            $.alertDefaultError();
        });
    };

    $('.main div[item]').each(function () {
        var item = $(this).attr('item');
        var classId = $(this).attr('classId');

        loadElements(item, classId);
    });

    $('body').on('click', '.main div[item] ul.header > li.h2', function() {
        var h2 = $(this);
        var display = h2.attr('display');
        var div = h2.parents('div[item]');
        var container = div.find('div[list]');
        var item = div.attr('item');
        var classId = div.attr('classId');

        if (display == 'show') {
            $.post('/moonlight/elements/close', {
                item: item,
                classId: classId
            }, function(data) {
                h2.attr('display', 'hide');
                container.hide();
            });
        } else if (display == 'hide') {
            $.post('/moonlight/elements/open', {
                item: item,
                classId: classId
            }, function(data) {
                h2.attr('display', 'show');
                container.show();
            });
        } else {
            $.getJSON('/moonlight/elements/list', {
                item: item,
                classId: classId,
                open: true
            }, function(data) {
                if (data.html) {
                    $('.main div[item="' + item + '"]').html(data.html);
                }
            });
        }
    });

    $('body').on('click', 'ul.pager > li[prev].active', function () {
        var pager = $(this).parent();
        var classId = pager.attr('classId');
        var item = pager.attr('item');
        var page = parseInt(pager.attr('page')) - 1;

        if (page < 1) page = 1;

        getElements(item, classId, page);
    });

    $('body').on('click', 'ul.pager > li[first].active', function () {
        var pager = $(this).parent();
        var classId = pager.attr('classId');
        var item = pager.attr('item');

        getElements(item, classId, 1);
    });

    $('body').on('keydown', 'ul.pager > li.page > input', function (event) {
        var pager = $(this).parents('ul.pager');
        var classId = pager.attr('classId');
        var item = pager.attr('item');
        var page = parseInt($(this).val());
        var last = parseInt(pager.attr('last'));
        var code = event.keyCode || event.which;
        
        if (code === 13) {
            if (page < 1) page = 1;
            if (page > last) page = last;

            getElements(item, classId, page);
        }
    });

    $('body').on('click', 'ul.pager > li[last].active', function () {
        var pager = $(this).parent();
        var classId = pager.attr('classId');
        var item = pager.attr('item');
        var last = pager.attr('last');

        getElements(item, classId, last);
    });

    $('body').on('click', 'ul.pager > li[next].active', function () {
        var pager = $(this).parent();
        var classId = pager.attr('classId');
        var item = pager.attr('item');
        var page = parseInt(pager.attr('page')) + 1;
        var last = parseInt(pager.attr('last'));

        if (page > last) page = last;

        getElements(item, classId, page);
    });
});