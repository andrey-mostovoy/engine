/*********************************************
 *********  GENERAL VALIDATOR INIT JS    ********
 ********************************************/

// variable for ajax form validation
var form;

/**
 * Responce on Ajax form submit action
 */
function forms_response(response)
{
    switch(response.result)
    {
        case "ok":
            if((response.content != undefined) && response.content != "") // if need to refresh form and add message
            {
                var api = $("ul.popup-tabs").data("tabs");
                api.getPanes().eq(0).load(api.getCurrentTab().find('a').attr("href"), 
                function(){
                    bind_validation();
 
                    cuSel({
                        changedEl: $('select', '#js_account_popup'),
                        visRows: 5,
                        scrollArrows: true
                    });
                }
                );
                $.ajaxer('setMessage',response.content);
            }
            else
            {
                window.location.reload();
            }
            break;
        case "error":
            $.ajaxer('setError',response.content);
            break;
        case "message":
            $.ajaxer('setMessage',response.content);
            break;    
        case "redirect":
            window.location = response.content;
            break;
    }
}

var validation_response = null;

// server side validation function response
function validatorResponse(response)
{
    // everything is ok.
    if (response.result === 'ok')
    {   // submit form
        form.unbind('submit');
        form.submit();
    }// everything is ok.
    else if (response.result === 'ok_ajax')
    {   // submit form
        form.ajaxSubmit(forms_response);
    }
    else if (response.result === 'confirm')
    {   // need some confirm
        return true;
    }
    else // server-side validation failed. use invalidate() to show errors
    {
        var messages = {};  // messages for input fields presented in form and not hidden
        var no_input_messages = {}; // messages for input fields NOT presented in form or hidden
        validation_response = response;
        
        for (var i in response.messages) {
            if($(':input[name="'+i+'"]', form).length == 0 || $(':input[name="'+i+'"]', form).is(':hidden'))
            {
                no_input_messages[i] = response.messages[i];
            }
            else
            {
                if (/\[\[/.test(i)) {
                    var key = i.replace('__data[[', '__data[');
                    key = key.replace(']]', ']');
                    messages[key] = response.messages[i];
                }
                else {
                    messages[i] = response.messages[i];
                }
            }
        }

        form.data("validator").invalidate(messages);
        
        if(Object.size(no_input_messages))
        {
            if(global.is_backend)
            {
            }
            else
            {
                for (var i in no_input_messages) {
                    $.ajaxer('setError', lang[i.match(/([\w\d])+/gi).pop()]+ " - " +no_input_messages[i]);
                }
            }
        }
        
        var first_error;
        var f_pos;
        $(document).find('div.errors').each(function(){
            if(f_pos > $(this).offset().top || f_pos == undefined)
            {
                first_error = $(this);
                f_pos = $(this).offset().top;
            }
        });
        if(!isScrolledIntoView(first_error))
        {
            $.scrollTo( first_error, 600, {offset: {top:-50}} );
        }
    }
}

function isScrolledIntoView(elem) 
{
    if(global.is_backend)
        return true;
    if(elem == undefined)
        return true;
    var docViewTop = $(window).scrollTop(); 
    var docViewBottom = docViewTop + $(window).height(); 
    var elemTop = elem.offset().top; 
    var elemBottom = elemTop + elem.height();
    return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom));     
}
 
$(function(){

    
    // bind ajax form validation
    bind_validation();
});

function bind_validation()
{
    // bind ajax form validation
    if ($(".js_v").length > 0)
    {
        var params = {};
        if(global.is_backend)
        {   // error show method on administration
            params.container = '.messages';
            params.effect = 'wall';

            $.tools.validator.addEffect("wall", function(errors, event) {
                // get the message wall
                var wall = $(this.getConf().container).fadeIn();
                
                if(validation_response != null && validation_response.tpl != '')
                {
                    wall.html( $(validation_response.tpl).html() )
                        .children('div').fadeIn();
                        
                    $('#manage-save tr').removeClass('error');
                    $.each(errors, function(index, error) {
                        error.input.parents('tr').eq(0).addClass('error');
                    });
                }
                else
                {
                    // remove all existing messages
                    wall.find("p").remove();

                    // add new ones
                    var k1 = k2 = k3 = 1;
                    $.each(errors, function(index, error) {

                        var str = '';
                        str = "<p><strong>" +lang[error.input.attr("name").match(/([\w\d])+/gi).pop()]+ "</strong> " +error.messages[0]+ "</p>";
                        
                        wall.append(
                            str
                        );
                    });
                }
            // the effect does nothing when all inputs are valid
            }, function(inputs)  {

            });
        }
        else
        {
            params.messageClass = 'errors';
//            params.message = '<div><div>';
//            params.offset = [0,5];
            params.position = 'top right';
        }
        
        // server side ajax validation
        $(".js_v")
            .validator(params)
            .submit(function(e) {
                form = $(this);

                if (!e.isDefaultPrevented()) {
                    var v_url = '';
                    if ( form.attr('action') == undefined || form.attr('action').length == 0)
                        v_url = global.url.address;
                    else
                    {
                        v_url = form.attr('action').split('/');
                        v_url.removeByValue('');
                        v_url.removeByValue(window.location.protocol);
                        v_url.removeByValue(window.location.hostname);
                        v_url.removeByValue(global.site_part);
                        
                        if(v_url.length ==0)
                            v_url = global.controller;
                        else
                            v_url = v_url.slice(0,1).join('/');
                        
                        v_url = [window.location.protocol, '', window.location.hostname+(global.site_part != '' ? '/'+global.site_part : ''), v_url].join('/');
                    }
                    // submit with AJAX
                    sendData(v_url + '/ajaxValidation',
                            form.serialize(),
                            validatorResponse,
                            "json");
                    // prevent default form submission logic
                    e.preventDefault();
                }
            })
            .bind('onBeforeValidate', function(e, els){
                // this is for validating dynamicly added fields
                $(this).validator(params);
            });
        
    }
}

Array.prototype.removeByValue = function(val) {
    for(var i=0; i<this.length; i++) {
        if(this[i] == val) {
            this.splice(i, 1);
            break;
        }
    }
}

function repositionValidationErrors()
{
    $(".js_v").data("validator").reflow();
}

function closeValidationErrors()
{
    $('div.errors').hide();
}
