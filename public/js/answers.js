$(function () {
    $('body').on('click', '.answers .answer', function() {
        var answer = $(this);
        var id = answer.attr('answer');

        if (! answer.hasClass('correct')) {
            answer.addClass('correct');
        }

        $.blockUI();

        $.post(
            '/plugins/answers/' + id,
            {},
            function(data) {
                $.unblockUI();

                if (data.answers) {
                    for (var id in data.answers) {
                        var correct = data.answers[id];
                        var answer = $('div[answer="' + id + '"]');

                        if (correct) {
                            answer.addClass('correct');
                        } else {
                            answer.removeClass('correct');
                        }
                    }
                }
            }
        );
    });
});