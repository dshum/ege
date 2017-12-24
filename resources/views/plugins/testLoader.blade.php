<script>
$(function() {
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
                } else if (data.ok) {
                    $('.ok').fadeIn(200);
                    $('input[name="title"]').val('');
                    $('select[name="topic"]').val('');
                    $('textarea[name="content"]').val('');
                }
            },
            error: function() {
                $.unblockUI();
            }
        });

        return false;
    });
});
</script>
<div class="ok">
    <div class="container">
        Тест успешно создан!
    </div>
</div>
<form action="/plugins/loader" method="POST">
    <div class="edit">
        <div class="row">
            <label>Название теста:</label><span name="title" class="error"></span><br>
            <input type="text" name="title" value="" placeholder="Название теста">
        </div>
        <div class="row">
            <label>Тема:</label><span name="topic" class="error"></span><br>
            <select name="topic">
                <option value="">- Выберите тему -</option>
                @foreach ($topics as $topic)
                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <label>Скопируйте текст с вопросами и вариантами ответов.<br>
            Вопросы должны разделяться двумя пустыми строками;<br>
            вопрос от ответов отделяется одной пустой строкой.</label><span name="content" class="error"></span><br>
            <textarea name="content" rows="15" style="width: 50rem;"></textarea>
        </div>
        <div class="row submit">
            <input type="submit" value="Загрузить" class="btn">
        </div>
    </div>
</form>