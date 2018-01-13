$(function () {
    $('body').on('click', '.answers .answer', function() {
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