jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

$(function() {
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
});