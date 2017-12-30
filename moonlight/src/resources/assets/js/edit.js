$(function() {
    var element = {};

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

        $('input.many').each(function() {
            var input = $(this);
            var item = $(this).attr('item');
            var name = $(this).attr('property');

            $(this).autocomplete({
                serviceUrl: '/moonlight/elements/autocomplete',
                params: {
                    item: item
                },
                onSelect: function (suggestion) {
                    element = suggestion;
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

        $('div[property].add').click(function() {
            var name = $(this).attr('property');
            var elements = $('.many.elements[name="' + name + '"]');
    
            if (element.id) {
                var checkbox = $('input:checkbox[name="' + name + '[]"][id="' + element.classId + '"]');

                if (checkbox.length) {
                    checkbox.prop('checked', true);
                } else {
                    elements.append('<p><input type="checkbox" name="' + name + '[]" id="' + element.classId + '" checked value="' + element.id + '"><label for="' + element.classId + '">' + element.value + '</label></p>');
                }

                element = {};
            }

            $('input:text[name="' + name + '_autocomplete"]').val('');
        });
    };

    $('body').on('change', ':file', function(e) {
        var name = $(this).attr('name');
        var path = e.target.files[0] ? e.target.files[0].name : 'Выберите файл';

        $('.file[name="' + name + '"]').html(path);    
        $('[name="' + name + '_drop"]').prop('checked', false);
    });

    $('body').on('click', '.file[name]', function() {
        var name = $(this).attr('name');
        var fileInput = $(':file[name="' + name + '"]');

        fileInput.click();
    });

    $('body').on('click', '.reset', function() {
        var name = $(this).attr('name');

        $('[name="' + name + 'drop"]').prop('checked', false);
        $('.file[name="' + name + '"]').html('Выберите файл');
        $(':file[name="' + name + '"]').val('');
    });

    tinymce.init({
        selector: 'textarea[tinymce="true"]',
        language: 'ru',
        plugins: ['lists', 'link', 'image', 'paste', 'table', 'code', 'preview'],
        width: '40rem',
        height: '20rem',
        convert_urls: false,
        setup: function(editor) {
            editor.on('keypress keydown', function(event) {
                return $.onCtrlS(event);
            });
        }
    });

    $('form').submit(function() {
        $('span.error').fadeOut(200);

        $('textarea[tinymce="true"]').each(function() {
            var name = $(this).attr('name');

			$(this).val(tinyMCE.get(name).getContent());
		});

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

    $('.button.save.enabled').click(function() {
        $('form').submit();
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