$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.onCtrlS = function(event) {
		if ( ! event) event = window.event;

		if (event.keyCode) {
			var code = event.keyCode;
		} else if (event.which) {
			var code = event.which;
		}

		if (code == 83 && event.ctrlKey == true) {
			$('form').submit();
			return false;
		}

		return true;
	};

    
    $.blockUI = function(handle) {
        $('.block-ui').fadeIn(100, handle);
    };
    
    $.unblockUI = function(handle) {
        setTimeout(function() {
            $('.block-ui').fadeOut(100, handle); 
        }, 200);
    };
    
    $.alert = function(content, handle) {
        if (content) {
            $('.alert .content').html(content);
        }
        $('.alert').fadeIn('fast', handle);
    };
    
    $.alertDefaultError = function(handle) {
        $('.alert .content').html('Произошла какая-то ошибка.<br>Обновите страницу.');
        $('.alert').fadeIn('fast', handle);
    };
    
    $.alertClose = function(handle) {
        $('.alert').fadeOut('fast', handle);
    };
    
    $.confirm = function(selector, content, handle) {
        var container = selector ? $(selector) : $('.confirm');
        
        if (content) {
            container.find('.content').html(content);
        }
        
        container.fadeIn('fast', handle);
    };
    
    $.confirmClose = function(selector, handle) {
        var container = selector ? $(selector) : $('.confirm');
        
        container.fadeOut('fast', handle);
    };

    $('body').keypress(function(event) {
		return $.onCtrlS(event);
	}).keydown(function(event) {
		return $.onCtrlS(event);
	});
    
    $('.alert .container').click(function(e) {
        return false;
    });
    
    $('.alert .hide').click(function() {
        $('.alert').fadeOut('fast');
    });
    
    $('.alert').click(function() {
        $('.alert').fadeOut('fast');
    });

    $('.confirm .container').click(function(e) {
        return false;
    });
    
    $('.confirm .cancel').click(function() {
        $('.confirm').fadeOut('fast');
    });
});