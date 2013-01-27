$(document).ready(function(){
    // handle login as canceled accaunt
    if(global.login_status_cancel != undefined)
    {
        if(confirm(global.login_status_cancel))
        {
            window.location.href = global.url.base+'/auth/restore';
        }
    }
    // ---- end
    //Login Popup
    $('#user_login_popup').click(function(){
        if ($('.login-popup').is(":hidden")) {
            $('.login-popup').fadeIn(500);
        } else {
            closeValidationErrors();
            $('.login-popup').fadeOut(500, function(){
                $('.forgot_f').hide();
            });
        }
        return false;
    });
    $('.login_f a.btn-close').click(function(){
        closeValidationErrors()
        $('.login-popup').fadeOut(500, function(){
            $('.forgot_f').hide();
        });
        return false;
    });
    // --- end
    // forgot popup
    $('#forgot_pass').click(function(){
        if($('.forgot_f').is(":hidden")) {
            $('.forgot_f').find('div.error').remove();
            $('.forgot_f').fadeIn(500);
        } else {
            $('.forgot_f').fadeOut(500);
        }
        return false;
    });
    $('.forgot_f a.btn-close').click(function(){
        $('.forgot_f').fadeOut(500);
        return false;
    });
    // --- end
});