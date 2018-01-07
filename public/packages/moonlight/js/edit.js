$(function() {
    var element = {};

    var init = function() {
        $('input[name].date').calendar({
            dateFormat: '%Y-%m-%d'
        });

        $('input.one').each(function() {
            var parent = $(this).parents('div.row');
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
                    parent.find('input:hidden[name="' + name + '"]').val(suggestion.id);
                    parent.find('span[container][name="' + name + '"]').html(suggestion.value);
                },
                minChars: 0
            });
        });

        $('input.many').each(function() {
            var input = $(this);
            var parent = $(this).parents('div.row');
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
                    element = suggestion;
                    parent.find('span[container][name="' + name + '"]').html(suggestion.value);
                },
                minChars: 0
            });
        });

        $('.addition.unset[property]').click(function() {
            var parent = $(this).parents('div.row');
            var name = $(this).attr('property');
    
            parent.find('input:hidden[name="' + name + '"]').val('');
            parent.find('input:text[name="' + name + '_autocomplete"]').val('');
            parent.find('span[container][name="' + name + '"]').html('Не определено');
        });

        $('.addition.reset[property]').click(function() {
            var parent = $(this).parents('div.row');
            var name = $(this).attr('property');
    
            parent.find('input:hidden[name="' + name + '"]').val(-1);
            parent.find('input:text[name="' + name + '_autocomplete"]').val('');
            parent.find('span[container][name="' + name + '"]').html('Не изменять');
        });

        $('.addition.add[property]').click(function() {
            var parent = $(this).parents('div.row');
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

            parent.find('input:text[name="' + name + '_autocomplete"]').val('');
            parent.find('span[container][name="' + name + '"]').html('');
        });
    };

    $('body').on('change', '.loadfile :file', function(e) {
        var name = $(this).attr('name');
        var path = e.target.files[0] ? e.target.files[0].name : 'Выберите файл';

        $('.file[name="' + name + '"]').html(path);    
        $('[name="' + name + '_drop"]').prop('checked', false);
    });

    $('body').on('click', '.loadfile .file[name]', function() {
        var name = $(this).attr('name');
        var fileInput = $(':file[name="' + name + '"]');

        fileInput.click();
    });

    $('body').on('click', '.loadfile .reset', function() {
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
                } else if (data.added && data.url) {
                    document.location.href = data.url;
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

    $('.button.copy.enabled').click(function() {
        $.confirm(null, '#copy');
    });

    $('.button.move.enabled').click(function() {
        $.confirm(null, '#move');
    });

    $('.button.delete.enabled').click(function() {
        $.confirm(null, '#delete');
    });

    $('.confirm .btn.copy').click(function() {
        var parent = $(this).parents('.confirm');
        var url = $(this).attr('url');

        if (! url) return false;

        $.confirmClose();
        $.blockUI();
        
        var one = null;
        
        parent.find('input[type="radio"]:checked:not(disabled), input[type="hidden"]').each(function() {
            var name = $(this).attr('property');
            var value = $(this).val();
            
            one = {
                name: name,
                value: value
            };
        });
        
        $.post(url, one, function(data) {
            $.unblockUI(function() {
                if (data.error) {
                    $.alert(data.error);
                } else if (data.copied && data.url) {
                    location.href = data.url;
                }
            });
        }).fail(function() {
            $.unblockUI();
            $.alertDefaultError();
        });
    });

    $('.confirm .btn.move').click(function() {
        var parent = $(this).parents('.confirm');
        var url = $(this).attr('url');

        if (! url) return false;
        
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
        
        $.post(url, one, function(data) {
            $.unblockUI(function() {
                if (data.error) {
                    $.alert(data.error);
                } else if (data.moved) {
                    location.reload();
                }
            });
        }).fail(function() {
            $.unblockUI();
            $.alertDefaultError();
        });
    });

    $('.confirm .btn.remove').click(function() {
        var url = $(this).attr('url');

        if (! url) return false;

        $.confirmClose();
        $.blockUI();

        $.post(url, {}, function(data) {
            $.unblockUI(function() {
                if (data.error) {
                    $.alert(data.error);
                } else if (data.deleted && data.url) {
                    location.href = data.url;
                }
            });
        }).fail(function() {
            $.unblockUI(); 
            $.alertDefaultError();
        });
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

    $('div.item').fadeIn(200);
});