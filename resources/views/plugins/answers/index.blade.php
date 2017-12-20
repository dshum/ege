<style>
.answers {
    width: 15rem;
}

.answers .answer {
    border-radius: 2px;
    padding: 0 5px;
    cursor: pointer;
}

.answers .answer:hover {
    background-color: lightskyblue;
}

.answers .answer.correct {
    background-color: lawngreen;
}
</style>
<script>
$(function () {
    $('.answers .answer').click(function() {
        var answer = $(this);
        var id = answer.attr('answer');

        if (answer.hasClass('correct')) return false;

        answer.parent().find('.answer.correct').removeClass('correct');
        answer.addClass('correct');

        $.blockUI();

        $.post(
            '/plugins/answers/' + id,
            {},
            function() {
                $.unblockUI();
            }
        );
    });
});
</script>