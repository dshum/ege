$(function() {
    var itemTotal = $('.main div[item]').length;
    var itemCount = 0;
    var empty = true;
    var checked = {};

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
        $.blockUI();

        $.getJSON('/moonlight/elements/list', {
            item: item,
            classId: classId,
            page: page
        }, function(data) {
            $.unblockUI();

            if (data.html && data.html.length) {
                $('.main div[item="' + item + '"]').html(data.html);
            } else {
                $('.main div[item="' + item + '"]').fadeOut(200);
            }
        }).fail(function() {
            $.unblockUI();
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
            h2.attr('display', 'hide');
            container.hide();

            $.post('/moonlight/elements/close', {
                item: item,
                classId: classId
            });
        } else if (display == 'hide') {
            h2.attr('display', 'show');
            container.show();

            $.post('/moonlight/elements/open', {
                item: item,
                classId: classId
            });
        } else {
            $.blockUI();

            $.getJSON('/moonlight/elements/list', {
                item: item,
                classId: classId,
                open: true
            }, function(data) {
                $.unblockUI();

                if (data.html) {
                    $('.main div[item="' + item + '"]').html(data.html);
                }
            });
        }
    });

    $('body').on('click', 'td.check', function() {
        var tr = $(this).parent();
        var itemContainer = $(this).parents('div[item]');
        var item = itemContainer.attr('item');
        var elementId = tr.attr('elementId');

        if (typeof checked[item] === 'undefined') {
            checked[item] = [];
        }

        var index = checked[item].indexOf(elementId);

        if (tr.hasClass('checked')) {
            if (index > -1) {
                checked[item].splice(index, 1);
            }

            tr.removeClass('checked');
        } else {
            if (index === -1) {
                checked[item].push(elementId);
            }

            tr.addClass('checked');
        }

        if (checked[item].length) {
            itemContainer.find('.button.copy').addClass('enabled');
            itemContainer.find('.button.move').addClass('enabled');
            itemContainer.find('.button.delete').addClass('enabled');
        } else {
            itemContainer.find('.button.copy').removeClass('enabled');
            itemContainer.find('.button.move').removeClass('enabled');
            itemContainer.find('.button.delete').removeClass('enabled');
        }
    });

    $('body').on('click', '.button.delete.enabled', function() {
        var itemContainer = $(this).parents('div[item]');
        var item = itemContainer.attr('item');

        $.confirm(null, '.confirm[id="' + item + '_delete"]');
    });

    $('body').on('click', '.confirm .btn.remove', function() {
        var itemContainer = $(this).parents('div[item]');
        var classId = itemContainer.attr('classId');
        var item = itemContainer.attr('item');

        $.confirmClose();
        $.blockUI();

        $.post(
            '/moonlight/elements/delete',
            {
                item: item,
                checked: checked[item]
            },
            function(data) {
                $.unblockUI();

                if (data.error) {
                    $.alert(data.error);
                } else if (data.deleted) {
                    getElements(item, classId);
                }
            }
        );
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

    $('.sidebar .elements .h2 span').click(function() {
        var block = $(this).parents('.elements');
        var rubric = block.attr('rubric');
        var display = block.attr('display');
        var ul = block.find('ul');

        if (display == 'show') {
            block.attr('display', 'hide');
            ul.hide();

            $.post('/moonlight/rubrics/close', {
                rubric: rubric
            });
            
        } else if (display == 'hide') {
            block.attr('display', 'show');
            ul.show();

            $.post('/moonlight/rubrics/open', {
                rubric: rubric
            });
        } else {
            $.blockUI();

            $.getJSON('/moonlight/rubrics/get', {
                rubric: rubric
            }, function(data) {
                $.unblockUI();

                if (data.html) {
                    block.append(data.html);
                    block.attr('display', 'show');
                }
            });
        }
    });
});