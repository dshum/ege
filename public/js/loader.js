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