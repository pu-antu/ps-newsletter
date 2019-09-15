(function ($) {
    'use strict';

    $('.ps-newsletter').on('submit', function(e){
        e.preventDefault();
        $(".ps_newsletter_submit").val('Wait..');
        var email = $('.ps-newsletter-email', this).val();
        var name = $('.ps-newsletter-name', this).val();
        var validation = true;
        if(email == '' || name == ''){
            alert('Email and Name feild is required');
            $(".ps_newsletter_submit").val('subscribe');
            validation = false;

        }
        if(validation){
            $.ajax({
            url : ps_check_obj.ajaxurl,
            type : 'post',
            dataType: "json",
            data : {
                action : 'psSubscribeForm',
                email : email,
                name : name,
                ps_security : ps_check_obj.ajax_nonce,
            },
            success : function( response ) {
                //console.log(response);
                if(response.status === 'success'){
                    alert('Subscribe successful');
                    $(".ps_newsletter_submit").val('subscribe');
                }else if(response.status === 'exists'){
                    alert('Email already exists');
                    $(".ps_newsletter_submit").val('subscribe');
                }else if(response.status === 'exists'){
                    alert('Something Wrong');
                    $(".ps_newsletter_submit").val('subscribe');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("Error: " + errorThrown);
            }
        });
        }
        
    });
})(jQuery);