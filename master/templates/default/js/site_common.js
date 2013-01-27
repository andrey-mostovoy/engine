/*********************************************
 *********  GENERAL SITE COMMON JS    ********
 ********************************************/

function close_ajax_message()
{
    $.ajaxer('closeMessage');
    return false;
}

// send post @data by @url by ajax and delegate to @success function
function sendData(url, data, success, type)
{
    $.ajaxer('sendData', url, data, success, type);
}

// common handle ajax response
// if callback function return true this launch default common 
// actions, if false - not
// response consist from result and content. Result can be next:
//  - ok        - all good
//  - error     - some errors
//  - message   - some message
//  - redirect  - redirect browser
//  - confirm   - need confirm from user
//  - html      - need insert some html to somewhere
// Content consist from some content
function ajaxResponse(response, success)
{
    $.ajaxer('ajaxResponse', response, success);
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};
 
$(function(){

	// Messages
	$('.messages>div').hide().fadeIn('slow');

	$('.messages .close').live('click', function() {
		$(this).parents('div').eq(0).fadeOut('slow', function() {$(this).remove();});
        return false;
	});
	// end Messages

    // bind onclick event for all submit links
    $('form a.js_btn_submit').live('click', function(event){
        $(this).parents('form').submit();
        return false;
    });
    $('form a.js_ajax_submit').live('click', function(event){
        $(this).parents('form').ajaxSubmit({
            success: function(r){
                $.ajaxer('replace', $(r.content));
            }
        });
        return false;
    });
    // bind Enter keypress as submit
    $('form:has(a.js_btn_submit)').live('keypress', function(event){
        if (event.keyCode == 13) {
            $(this).submit();
            return false;
        }
    });
    
    // bind onclick event for all reset links
    $('form a.js_btn_reset').live('click', function(event){
        $(this).parents('form').find(':input')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');
        return false;
    });

    //bind handler for ajax page action
    //must return html with id, that content
    //replace excisted on page by id
    if ($('.js_ajax').length > 0)
    {
        $('.js_ajax').ajaxer({
            after : function(content) {
                lightEven();
            }
        });
    }
    //bind handler for ajax page action,
    // the same as above, but append content
    //not replace
    if ($('.js_ajax_append').length > 0)
    {
        $('.js_ajax_append').ajaxer({
            'method' : 'append',
            after : function(content) {
                lightEven();
            }
        });
    }
});

function embedFlash(id, url, size, flashvars, params, attributes)
{
    if(flashvars == undefined)
    {
        flashvars = {};
    }
    if(params == undefined)
    {
        params = {};
        params.wmode = 'transparent';
    }
    if(attributes == undefined)
    {
        attributes = {};
    }
    
    swfobject.embedSWF(
        url,
        id,
        size.w,
        parseInt(size.h)+38,
        "9.0.0",
        false,
        flashvars,
        params,
        attributes
    );
}