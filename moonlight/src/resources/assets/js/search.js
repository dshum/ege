jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

$(function() {
    var checked = {};

    var init = function(item) {
        $('div[item="' + item + '"] input.one').each(function() {
            var parent = $(this).parents('div.row');
            var relatedItem = $(this).attr('item');
            var name = $(this).attr('property');
    
            $(this).autocomplete({
                serviceUrl: '/moonlight/elements/autocomplete',
                params: {
                    item: relatedItem
                },
                formatResult: function(suggestion, currentValue) {
                    return suggestion.value + ' <small>(' + suggestion.id + ')</small>';
                },
                onSelect: function (suggestion) {
                    parent.find('input:hidden[name="' + name + '"]').val(suggestion.id);
                    parent.find('span[container][name="' + name + '"]').html(suggestion.value);
                },
                minChars: 0
            });
        });
    };

    var getElements = function(item, page) {
        $.blockUI();

        $('form#search-form').ajaxSubmit({
            url: '/moonlight/search/list',
            dataType: 'json',
            data: {
                item: item,
                page: page
            },
            success: function(data) {
                $.unblockUI();
            
                if (data.html) {
                    $('.list-container').html(data.html);

                    init(item);
                }
            },
            error: function() {
                $.unblockUI();
                $.alertDefaultError();
            }
        });
    };

    $('#filter').keyup(function () {
        var str = $(this).val();

        if (str.length > 0) {
            $('ul.items > li:not(:contains("' + str + '"))').hide();
            $('ul.items > li:contains("' + str + '")').show();
        } else {
            $('ul.items > li').show();
        }
    }).change(function () {
        var str = $(this).val();

        if (str.length > 0) {
            $('ul.items > li:not(:contains("' + str + '"))').hide();
            $('ul.items > li:contains("' + str + '")').show();
        } else {
            $('ul.items > li').show();
        }
    });

    $('.search-form-links div.link').click(function() {
        var item = $(this).attr('item');
        var name = $(this).attr('name');
        var active = ! $(this).hasClass('active');

        $(this).toggleClass('active');
        $('.search-form-params div.block[name="' + name + '"]').toggleClass('active');

        $.post('/moonlight/search/active/' + item + '/' + name, {
            active: active
        });
    });

    $('.search-form-params div.close').click(function() {
        var item = $(this).attr('item');
        var name = $(this).attr('name');

        $('.search-form-links div.link[name="' + name + '"]').removeClass('active');
        $('.search-form-params div.block[name="' + name + '"]').removeClass('active');

        $.post('/moonlight/search/active/' + item + '/' + name, {
            active: false
        });
    });

    $('input[name].date').calendar({
        dateFormat: '%Y-%m-%d'
    });

    $('input.one').each(function() {
        var item = $(this).attr('item');
        var name = $(this).attr('property');

        $(this).autocomplete({
            serviceUrl: '/moonlight/elements/autocomplete',
            params: {
                item: item
            },
            formatResult: function(suggestion, currentValue) {
                return suggestion.value + ' <small>(' + suggestion.id + ')</small>';
            },
            onSelect: function (suggestion) {
                $('input:hidden[name="' + name + '"]').val(suggestion.id);
            },
            minChars: 0
        });
    });

    $('.addition.unset[property]').click(function() {
        var name = $(this).attr('property');

        $('input:hidden[name="' + name + '"]').val('');
        $('input:text[name="' + name + '_autocomplete"]').val('');
    });

    $('body').on('click', 'th.check', function() {
        var tr = $(this).parent();
        var table = tr.parents('table');
        var itemContainer = $(this).parents('div[item]');
        var item = itemContainer.attr('item');

        if (typeof checked[item] === 'undefined') {
            checked[item] = [];
        }

        if (tr.hasClass('checked')) {
            checked[item] = [];

            tr.removeClass('checked');

            table.find('tbody tr').each(function() {
                $(this).removeClass('checked');
            });
        } else {
            tr.addClass('checked');

            table.find('tbody tr').each(function() {
                var elementId = $(this).attr('elementId');
                var index = checked[item].indexOf(elementId);

                if (index === -1) {
                    checked[item].push(elementId);
                }

                $(this).addClass('checked');
            });
        }

        if (checked[item].length) {
            itemContainer.find('.button.copy:not(.disabled)').addClass('enabled');
            itemContainer.find('.button.move:not(.disabled)').addClass('enabled');
            itemContainer.find('.button.bind:not(.disabled)').addClass('enabled');
            itemContainer.find('.button.unbind:not(.disabled)').addClass('enabled');
            itemContainer.find('.button.delete:not(.disabled)').addClass('enabled');
        } else {
            itemContainer.find('.button.copy:not(.disabled)').removeClass('enabled');
            itemContainer.find('.button.move:not(.disabled)').removeClass('enabled');
            itemContainer.find('.button.bind:not(.disabled)').removeClass('enabled');
            itemContainer.find('.button.unbind:not(.disabled)').removeClass('enabled');
            itemContainer.find('.button.delete:not(.disabled)').removeClass('enabled');
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
            itemContainer.find('.button.copy:not(.disabled)').addClass('enabled');
            itemContainer.find('.button.move:not(.disabled)').addClass('enabled');
            itemContainer.find('.button.bind:not(.disabled)').addClass('enabled');
            itemContainer.find('.button.unbind:not(.disabled)').addClass('enabled');
            itemContainer.find('.button.delete:not(.disabled)').addClass('enabled');
        } else {
            itemContainer.find('.button.copy:not(.disabled)').removeClass('enabled');
            itemContainer.find('.button.move:not(.disabled)').removeClass('enabled');
            itemContainer.find('.button.bind:not(.disabled)').removeClass('enabled');
            itemContainer.find('.button.unbind:not(.disabled)').removeClass('enabled');
            itemContainer.find('.button.delete:not(.disabled)').removeClass('enabled');
        }
    });

    $('body').on('click', '.button.copy.enabled', function() {
        var itemContainer = $(this).parents('div[item]');
        var item = itemContainer.attr('item');

        $.confirm(null, '.confirm[id="' + item + '_copy"]');
    });

    $('body').on('click', '.button.move.enabled', function() {
        var itemContainer = $(this).parents('div[item]');
        var item = itemContainer.attr('item');

        $.confirm(null, '.confirm[id="' + item + '_move"]');
    });

    $('body').on('click', '.button.bind.enabled', function() {
        var itemContainer = $(this).parents('div[item]');
        var item = itemContainer.attr('item');

        $.confirm(null, '.confirm[id="' + item + '_bind"]');
    });

    $('body').on('click', '.button.unbind.enabled', function() {
        var itemContainer = $(this).parents('div[item]');
        var item = itemContainer.attr('item');

        $.confirm(null, '.confirm[id="' + item + '_unbind"]');
    });

    $('body').on('click', '.button.delete.enabled', function() {
        var itemContainer = $(this).parents('div[item]');
        var item = itemContainer.attr('item');

        $.confirm(null, '.confirm[id="' + item + '_delete"]');
    });

    $('body').on('click', '.confirm .btn.copy', function() {
        var itemContainer = $(this).parents('div[item]');
        var parent = $(this).parents('.confirm');
        var item = itemContainer.attr('item');

        var name, value;
        
        parent.find('input[type="radio"]:checked:not(:disabled), input[type="hidden"]').each(function() {
            name = $(this).attr('property');
            value = $(this).val();
        });

        $.confirmClose();
        $.blockUI();

        $.post(
            '/moonlight/elements/copy',
            {
                item: item,
                checked: checked[item],
                name: name,
                value: value
            },
            function(data) {
                $.unblockUI(function() {
                    if (data.error) {
                        $.alert(data.error);
                    } else if (data.copied && data.url) {
                        location.href = data.url;
                    }
                });
            }
        );
    });

    $('body').on('click', '.confirm .btn.move', function() {
        var itemContainer = $(this).parents('div[item]');
        var parent = $(this).parents('.confirm');
        var item = itemContainer.attr('item');

        var one = null;
        
        parent.find('input[type="radio"]:checked:not(:disabled), input[type="hidden"]').each(function() {
            var name = $(this).attr('property');
            var value = $(this).val();
            
            one = {
                name: name,
                value: value
            };
        });

        if (! one) return false;

        $.confirmClose();
        $.blockUI();

        $.post(
            '/moonlight/elements/move',
            {
                item: item,
                checked: checked[item],
                name: one.name,
                value: one.value
            },
            function(data) {
                $.unblockUI(function() {
                    if (data.error) {
                        $.alert(data.error);
                    } else if (data.moved && data.url) {
                        location.href = data.url;
                    }
                });
            }
        );
    });

    $('body').on('click', '.confirm .btn.bind', function() {
        var itemContainer = $(this).parents('div[item]');
        var parent = $(this).parents('.confirm');
        var item = itemContainer.attr('item');

        var ones = {};
        var count = 0;
        
        parent.find('input[type="radio"]:checked:not(:disabled), input[type="hidden"]').each(function() {
            var name = $(this).attr('property');
            var value = $(this).val();
            
            if (value) {
                ones[name] = value;
                count++;
            }
        });

        if (! count) return false;

        $.confirmClose();
        $.blockUI();

        $.post(
            '/moonlight/elements/bind',
            {
                item: item,
                checked: checked[item],
                ones: ones
            },
            function(data) {
                if (data.error) {
                    $.unblockUI(function() {
                        $.alert(data.error);
                    });
                } else if (data.attached) {
                    getElements(item);
                }
            }
        );
    });

    $('body').on('click', '.confirm .btn.unbind', function() {
        var itemContainer = $(this).parents('div[item]');
        var parent = $(this).parents('.confirm');
        var item = itemContainer.attr('item');

        var ones = {};
        var count = 0;
        
        parent.find('input[type="radio"]:checked:not(:disabled), input[type="hidden"]').each(function() {
            var name = $(this).attr('property');
            var value = $(this).val();
            
            if (value) {
                ones[name] = value;
                count++;
            }
        });

        if (! count) return false;

        $.confirmClose();
        $.blockUI();

        $.post(
            '/moonlight/elements/unbind',
            {
                item: item,
                checked: checked[item],
                ones: ones
            },
            function(data) {
                if (data.error) {
                    $.unblockUI(function() {
                        $.alert(data.error);
                    });
                } else if (data.detached) {
                    getElements(item);
                }
            }
        );
    });

    $('body').on('click', '.confirm .btn.remove', function() {
        var itemContainer = $(this).parents('div[item]');
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
                    $.unblockUI(function() {
                        $.alert(data.error);
                    });
                } else if (data.deleted) {
                    getElements(item);
                }
            }
        );
    });

    $('body').on('click', 'ul.pager > li[prev].active', function () {
        var pager = $(this).parent();
        var item = pager.attr('item');
        var page = parseInt(pager.attr('page')) - 1;

        if (page < 1) page = 1;

        getElements(item, page);
    });

    $('body').on('click', 'ul.pager > li[first].active', function () {
        var pager = $(this).parent();
        var item = pager.attr('item');

        getElements(item, 1);
    });

    $('body').on('keydown', 'ul.pager > li.page > input', function (event) {
        var pager = $(this).parents('ul.pager');
        var item = pager.attr('item');
        var page = parseInt($(this).val());
        var last = parseInt(pager.attr('last'));
        var code = event.keyCode || event.which;
        
        if (code === 13) {
            if (page < 1) page = 1;
            if (page > last) page = last;

            getElements(item, page);
        }
    });

    $('body').on('click', 'ul.pager > li[last].active', function () {
        var pager = $(this).parent();
        var item = pager.attr('item');
        var last = pager.attr('last');

        getElements(item, last);
    });

    $('body').on('click', 'ul.pager > li[next].active', function () {
        var pager = $(this).parent();
        var item = pager.attr('item');
        var page = parseInt(pager.attr('page')) + 1;
        var last = parseInt(pager.attr('last'));

        if (page > last) page = last;

        getElements(item, page);
    });
});