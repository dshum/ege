$(function() {
    var init = function() {
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
            $('span[container][name="' + name + '"]').html('Не определено');
        });
    };

    $('form').submit(function() {
        $('span.error').fadeOut(200);
        $.blockUI();

        $(this).ajaxSubmit({
            url: this.action,
            dataType: 'json',
            success: function(data) {
                $.unblockUI();
                
                if (data.error) {
                    $.alert(data.error);
                } else if (data.errors) {
                    for (var field in data.errors) {
                        $('span.error[name="' + field + '"]')
                            .html(data.errors[field])
                            .fadeIn(200);
                    }
                } else if (data.added) {
                    
                } else if (data.views) {
                    for (var field in data.views) {
                        $('div.row[name="' + field + '"]')
                            .html(data.views[field]);
                    }

                    init();
                }
            },
            error: function() {
                $.unblockUI();
            }
        });

        return false;
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

    init();
});