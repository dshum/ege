$(function() {
    var getPhotos = function() {
        $.getJSON('/plugins/photos', {}, function(data) {
            if (data.error) {
                $.alert(data.error);
            } else if (data.photos) {
                var container = $('.photo-container');
                
                for (var index in data.photos) {
                    var filename = data.photos[index];
                    var html = 
                        '<div class="block" filename="' + filename + '">'
                        + '<span class="close">&#215;</span>'
                        + '<img src="/pictures/'+ filename + '"><br>'
                        + filename
                        + '</div>';
                    var block = $(html).hide();

                    container.append(block);
                    block.fadeIn(200);
                }
            }
        }).fail(function() {
            $.alertDefaultError();
        });
    };

    $('body').on('change', ':file', function(e) {
        var name = $(this).attr('name');
        var path = e.target.files[0] ? e.target.files[0].name : 'Выберите файл';

        $('.file[name="' + name + '"]').html(path);    
    });

    $('body').on('click', '.file[name]', function() {
        var name = $(this).attr('name');
        var fileInput = $(':file[name="' + name + '"]');

        fileInput.click();
    });

    $('body').on('click', '.reset', function() {
        var name = $(this).attr('name');

        $('.file[name="' + name + '"]').html('Выберите файл');
        $(':file[name="' + name + '"]').val('');
    });

    $('form[name="photo-loader"]').submit(function() {
        $('span.error').fadeOut(200);
        $('div.ok').fadeOut(200);

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
                } else if (data.loaded) {
                    $('.file[name="photo"]').html('Выберите файл');
                    $(':file[name="photo"]').val('');

                    var container = $('.photo-container');
                    var filename = data.loaded;
                    var html = 
                        '<div class="block" filename="' + filename + '">'
                        + '<span class="close">&#215;</span>'
                        + '<img src="/pictures/'+ filename + '"><br>'
                        + filename
                        + '</div>';
                    var block = $(html).hide();

                    container.prepend(block);
                    block.fadeIn(200);
                }
            },
            error: function() {
                $.unblockUI();
            }
        });

        return false;
    });

    $('body').on('click', 'span.close', function() {
        var span = $(this);
        var block = span.parents('div.block[filename]');
        var filename = block.attr('filename');

        $.post('/plugins/photos/delete', {
            filename: filename
        }, function(data) {
            if (data.error) {
                $.alert(data.error);
            } else if (data.deleted) {
                block.css({opacity: '0.5'});
                span.remove();
            }
        });
    });

    getPhotos();
});