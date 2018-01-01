$(function() {
    $('.button.enabled.restore').click(function() {
        $.confirm('#restore');
    });

    $('.button.enabled.delete').click(function() {
        $.confirm('#delete');
    });

    $('.confirm .restore').click(function() {
        var url = $(this).attr('url');

        if (! url) return false;

        $.confirmClose('#restore');
        $.blockUI();

        $.post(
            url,
            {},
            function(data) {
                $.unblockUI();

                if (data.error) {
                    $.alert(data.error);
                } else if (data.restored && data.url) {
                    document.location.href = data.url;
                }
            }
        );
    });

    $('.confirm .remove').click(function() {
        var url = $(this).attr('url');

        if (! url) return false;

        $.confirmClose('#delete');
        $.blockUI();

        $.post(
            url,
            {},
            function(data) {
                $.unblockUI();

                if (data.error) {
                    $.alert(data.error);
                } else if (data.deleted && data.url) {
                    document.location.href = data.url;
                }
            }
        );
    });

    $('div.item').fadeIn(200);
});