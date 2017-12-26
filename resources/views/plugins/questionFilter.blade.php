<style>
.question-filter {
    margin: 0;
    padding: 0;
}

.question-filter ul {
    margin: 0;
    padding: 0;
}

.question-filter ul li {
    display: inline-block;
    margin: 0 0.5rem;
    padding: 0;
}

.question-filter ul li select {
    margin: 0;
    height: 2rem;
}

.question-filter ul li input {
    margin: 0;
    height: 2rem;
}
</style>
<script>
$(function () {
    $('form#question-filter-form').submit(function() {
        var itemBlock = $(this).parents('div[item]');
        var classId = itemBlock.attr('classId');
        var item = itemBlock.attr('item');
        var text = $('.question-filter input[name="text"]').val();

        $.post('/plugins/questions/filter', {
            text: text
        }, function(data) {
            $.blockUI();

            $.getJSON('/moonlight/elements/list', {
                item: item,
                classId: classId
            }, function(data) {
                $.unblockUI();
    
                if (data.html) {
                    itemBlock.html(data.html);
                }
            });
        });

        return false;
    });
});
</script>
<div class="question-filter">
    <form id="question-filter-form">
        <ul>
            <li>
                <input type="text" name="text" value="{{ $text }}" placeholder="Текст вопроса">
            </li>
            <li>
                <input type="submit" value="Показать" class="btn small">
            </li>
        </ul>
    </form>
</div>