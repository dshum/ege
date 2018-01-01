$(function() {
    $('.button.delete.enabled').click(function() {
        $.confirm();
    });

    $('.confirm .remove').click(function() {
        var url = $(this).attr('url');

        if (! url) return false;

        $.confirmClose();
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