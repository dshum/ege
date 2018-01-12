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
<h2>Загруженные изображения</h2>
<div class="photo-container"></div>