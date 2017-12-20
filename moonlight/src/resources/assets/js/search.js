jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

$(function() {
    var getElements = function(item, page) {
        $.blockUI();

        $('form').ajaxSubmit({
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
            onSelect: function (suggestion) {
                $('input:hidden[name="' + name + '"]').val(suggestion.id);
            },
            minChars: 0
        });
    });

    $('div[property].reset').click(function() {
        var name = $(this).attr('property');

        $('input:hidden[name="' + name + '"]').val('');
        $('input:text[name="' + name + '_autocomplete"]').val('');
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