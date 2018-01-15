$(function() {
    $('form').submit(function() {
        $.blockUI();

        $(this).ajaxSubmit({
            url: this.action,
            dataType: 'json',
            success: function(data) {
                $.unblockUI();
                
                if (data.error) {
                    $('div.error').html(data.error).fadeIn(200);
                } else if (data.url) {
                    document.location.href = data.url;
                }
            },
            error: function() {
                $.unblockUI();
            }
        });

        return false;
    });
});