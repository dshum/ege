<style>
.photo-container {
    margin: 1rem 0;
    clear: both;
}

.photo-container .block {
    float: left;
    margin-right: 2rem;
    margin-bottom: 2rem;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 2px;
    box-shadow: 0 3px 7px 0 rgba(0, 0, 0, 0.18), 0 2px 11px 0 rgba(0, 0, 0, 0.15);
    padding: 0.5rem;
    background-color: white;
}

.photo-container .block img {
    max-height: 10rem;
    width: auto;
    margin-bottom: 0.25rem;
}
</style>
<script>
$(function() {
    var getPhotos = function() {
        $.getJSON('/plugins/photos', {}, function(data) {
            if (data.photos) {
                var container = $('.photo-container');
                
                for (var index in data.photos) {
                    var filename = data.photos[index];
                    var html = 
                        '<div class="block">' 
                        + '<img src="/pictures/'+ filename + '"><br>'
                        + filename
                        + '</div>';
                    var block = $(html);

                    container.append(block);
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

    $('form').submit(function() {
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
                        '<div class="block">' 
                        + '<img src="/pictures/'+ filename + '"><br>'
                        + filename
                        + '</div>';
                    var block = $(html);

                    container.prepend(block);
                }
            },
            error: function() {
                $.unblockUI();
            }
        });

        return false;
    });

    getPhotos();
});
</script>
<div class="ok">
    <div class="container">
        Изображение загружено!
    </div>
</div>
<form action="/plugins/photos/load" method="POST">
    <div class="edit">
        <div class="row">
            <label>Изображение:</label><span name="photo" class="error"></span><br>
            <div><small class="red">Допустимые форматы файла: GIF, JPG, PNG</small></div>
            <div class="loadfile">
                <div class="file" name="photo">Выберите файл</div>
                <span class="reset" name="photo" file>&#215;</span>
                <div class="file-hidden"><input type="file" name="photo"></div>
            </div>
        </div>
        <div class="row submit">
            <input type="submit" value="Загрузить" class="btn">
        </div>
    </div>
</form>
<br>
<h2>Загруженные изображения</h2>
<div class="photo-container"></div>