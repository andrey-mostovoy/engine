/*********************************************
 *************  GENERAL ADMIN JS    **********
 ********************************************/
$(function(){
// new admin style    
    $(".niceCheck").live('mousedown',function () {
        changeCheck($(this));
    });
    $(".niceCheck").each(function () {
        changeCheckStart($(this));
    });
    $(".niceRadio").each(function () {
        changeRadioStart($(this));
    });
    // i do it in tpl via smarty
//    $("table.listing tr:nth-child(even)").addClass("light");
//    
//    moved to manage.js
//    $("input[type=file]").each(function () {
//        $(this).filestyle({
//            image: "/templates/default/skins/admin/images/forms/buttons/btn-browse.png",
//            imageheight : 28,
//            imagewidth : 82,
//            width : 250
//        });
//    });
    $(".header-container > ul > li").hover(
        function () {$(this).children('ul').stop(true, true).slideDown('fast');},
        function () {$(this).children('ul').stop(true, true).hide();}
    );
    $('.header-container > ul > li').has('ul').addClass("dropdown").children('a').append('<span>&nbsp;</span>');
    
    //date - moved to manage.js
//    if($('#datepicker, #datepicker_from, #datepicker_to').length > 0)
//    {
//        $('#datepicker, #datepicker_from, #datepicker_to').dateinput({
//            format : 'dd/mm/yyyy',
//            min: -1
//        });
//    }
//    $('#dialog_link, ul#icons li').each(function () {
//        $(this).hover(
//            function() {$(this).addClass('ui-state-hover');},
//            function() {$(this).removeClass('ui-state-hover');}
//        );
//    });
//    $('#datepicker-icon, #datepicker-icon-from, #datepicker-icon-to').each(function () {
//        $(this).datepicker({
//            showOn: "button",
//            buttonImage: "images/icons/ico-calendar.png",
//            buttonImageOnly: true
//        });
//    });
    //end date
    
    // wtf?
    $('.thumb').each(function() {
        $(this).hover(
            function() {$(this).has('a.delete-item').children('a.delete-item').show()},
            function() {$(this).has('a.delete-item').children('a.delete-item').hide()}
        );
    });
    
// new admin style    
//    moved to table.js
//    $('#main_table th :checkbox').live('change', function(){
//        $('#main_table td :checkbox').prop('checked', $(this).prop('checked')).eq(0).change();
//        
//        $(".niceCheck").each(function () {
//            changeCheckStart($(this));
//        });
//    });
//
//    $('#main_table td :checkbox').live('change', function(){
//        if( $(":checkbox:checked", '#main_table td').length > 0 )
//        {
//            showGroupActionButtons();
//        }       
//        else if($(":checkbox:checked", '#main_table td').length == 0 )
//        {
//            hideGroupActionButtons();
//        }
//        if( $(":checkbox:checked", '#main_table td').length == 1 )
//        {
//            addIdToSingleActionButtons();
//            showSingleActionButtons();
//        }       
//        else if($(":checkbox:checked", '#main_table td').length == 0 
//            || $(":checkbox:checked", '#main_table td').length > 1
//        ) {
//            hideSingleActionButtons();
//        }
//    });
//--------------------



//    
    // show if checked any checkbox or hide delete selected buttons 
//    $(":input[name^='item_']").change(function(){
//        if( $(":input[name^='item_']:checked").length > 0 && $(".delete_selected").is(":hidden"))
//        {
//            $(".delete_selected").show();
//            $(".send_mails").show();
//        }       
//        else if($(":input[name^='item_']:checked").length == 0 && $(".delete_selected").is(":visible"))
//        {
//            $(".delete_selected").hide();
//            $(".send_mails").hide();
//        }
//    });

// moved to table.js
//    if($('#main_table').length > 0)
//    {
//        $('#main_table tr').hover(
//            function(){
//                $(this)
//                    .data('old_bg_color', $(this).css('background-color'))
//                    .css('background-color', '#d0dce8');
//            },
//            function(){
//                $(this)
//                    .css('background-color', $(this).data('old_bg_color'));
//            }
//        );
//    }
// -------------
    
    //something old
    // initialize setting object
//    if(global.controller != 'settings' && global.action == 'settings')
//    {
//        new Settings();
//    }
        
//    $('.save').click(function(){return save();});


    /*********************************************
     **********  PLACE YOUR CODE BELOW    ********
     ********************************************/
});
// moved to table.js
//function showGroupActionButtons()
//{
//    $('.js_group_action:hidden', '.actions').fadeIn('fast');
//}
//
//function hideGroupActionButtons()
//{
//    $('.js_group_action:visible', '.actions').fadeOut('fast');
//}
//
//function showSingleActionButtons()
//{
//    $('.js_single_action:hidden', '.actions').fadeIn('fast');
//}
//
//function hideSingleActionButtons()
//{
//    $('.js_single_action:visible', '.actions').fadeOut('fast');
//}
//
//function addIdToSingleActionButtons()
//{
//    $('.js_single_action', '.actions').each(function(){
//        $('a', this).attr('href', 
//            $('a', this).attr('href').split('/').slice(0, -1).join('/') 
//            + '/' + $(":checkbox:checked", '#main_table td').val() 
//        );
//    });
//}
//-----


// new  admin style
function changeCheck(el) {
    var input = el.find("input").eq(0);
    if (!input.prop("checked")) {
        input.prop("checked", true);
        el.css("background-position","0 -16px");
        input.change();
    }
    else {
        input.prop("checked", false);
        el.css("background-position","0 0");
        input.change();
    }
    return true;
}

function changeCheckStart(el) {
    var input = el.find("input").eq(0);
    if (input.prop("checked")) {
        el.css("background-position","0 -16px");
    }
    else
    {
        el.css("background-position","0 0");
    }
    return true;
}

function showpp() {
    if ($("#custom-message")) {
        $("#custom-message").showPopup();
    }
}

function hidepp() {
    if ($("#custom-message")) {
        $("#custom-message").hidePopup();
    }
}

function showpp_thumb_property() {
    if ($("#thumb-edit")) {
        $("#thumb-edit").showPopup();
    }
}

function hidepp_thumb_property() {
    if ($("#thumb-edit")) {
        $("#thumb-edit").hidePopup();
    }
}
// new admin style


////  check uncheck all checkboxes   - moved to table.js
//function toggle_all()
//{
////    console.log('asd')
////    $('.noform th :checkbox').click(function(){
////        $('.noform td :checkbox').prop('checked', $(this).prop('checked')).eq(0).change();
////    });
//    
////    $(":checkbox[name^='item_']").prop('checked', $(":checkbox[name='all_items']").prop('checked')).eq(0).change();
////    $(":checkbox[name^='item_']").attr('checked', $(":checkbox[name='all_items']").is(':checked') ).change();
//}

/** - moved to manage.js
 * save action
 */
//function save()
//{
//    //console.log($("#manage-save .ckeditor").length);
//    //if we have ckeditor content we need update reletive field first
//    if($("#manage-save .ckeditor").length > 0)
//    {
//        for ( instance in CKEDITOR.instances )
//        {
//            CKEDITOR.instances[instance].updateElement();
//        }
//    }
//    $("form#manage-save").submit();
//    return false;
//}

///** - moved to table.js
// * delete item responce function complete
// */
//function delete_complete(responce)
//{
//    if(responce.result == 'ok')
//    {
//        if($("#main_table tr").length - responce.content.length <= 2)
//        {
//            window.location.reload();
//        }
//        var ids = [];
//        $.each(responce.content, function(){
//            ids.push("#line_"+this);
//        });
//        $(ids.join(', ')).slideUp().remove();
//        $('.js_group_action, .js_single_action').hide();
//    }
//}

///** - moved to table.js
// * delete confirmation
// */
//function confirm_delete(text)
//{
//    if(text == undefined)
//    {
//        text = lang.are_you_sure;
//    }
//    if(!confirm(text))
//    {
//        return false;
//    }
//    return true;
//}

///** - moved to table.js
// * delete item action
// */
//function delete_item(el)
//{
//    if(!confirm_delete())
//    {
//        return false;
//    }
//    sendData($(el).attr('href'), '', delete_complete, 'json');
//    return false;
//}

/**
 * reset password responce function complete
 */
//function reset_complete(responce)
//{
//    if(responce.result == 'ok')
//    {
//        alert("Reset done");
//    }
//    else
//    {
//        alert("Reset failed");
//    }
//}

/**
 * reset password action
 */
//function reset_password(el)
//{
//    if(!confirm(lang.are_you_sure))
//    {
//        return false;
//    }
//    sendData($(el).attr('href'), '', reset_complete, 'json');
//    return false;
//}

///** - moved to table.js
// * Delete selected items
// */
//function group_delete(el)
//{
//    if(!confirm(lang.are_you_sure))
//    {
//        return false;
//    }
//    var ids = [];
//    $(":input[name^='item_']:checked").each(function(){
//        ids.push( $(this).val() );
//    });
//    sendData($(el).attr('href'), {ids: ids}, delete_complete, 'json');
//    return false;
//}
//
///**
// * send mails to selected users
// */
//function send_mails()
//{
//    $("#filter-apply").attr('action', 'users/sendmails');
//    $("#filter-apply").submit();
//    window.location = "users/sendmails";
//}
/*
	function changeCheckStart(el)

	{
	var el = el,
	        input = el.find("input").eq(0);
	      if(input.attr("checked")) {
	        el.css("background-position","0 -16px");
	        }
	     return true;
	}


function order(el)
{
    console.log('admin/common.js');
    sendData($(el).attr('href'), '', order_complete, 'json');
    return false;
}

function order_complete(responce)
{
    if(responce.result == 'ok' && responce.res)
    {
        var dur = 500;
        var el = $("#"+responce.id);
        var repl = '<tr class="repl"><td></td></tr>';
        switch(responce.dir)
        {
            case 'up':
                var comp_el = el.prev();
                el.after(repl).css('position', 'absolute');
                comp_el.after(repl).css('position', 'absolute');
                $(".repl").height( el.height() ).width( el.width() );
                el.animate({
                    top: '-='+el.height()
                  }, dur, function() {
                    // Animation complete.
                    el.css('position', 'relative');
                  });
                comp_el.animate({
                    top: '+='+comp_el.height()
                  }, dur, function() {
                    // Animation complete.
                    comp_el.css('position', 'relative');
                    $(".repl").remove();
                    el.insertBefore(comp_el);
                  });
            break;
            case 'down':
                var comp_el = el.next();
                el.after(repl).css('position', 'absolute');
                comp_el.after(repl).css('position', 'absolute');
                $(".repl").height( el.height() ).width( el.width() );
                el.animate({
                    top: '+='+el.height()
                  }, dur, function() {
                    // Animation complete.
                    el.css('position', 'relative');
                  });
                comp_el.animate({
                    top: '-='+comp_el.height()
                  }, dur, function() {
                    // Animation complete.
                    comp_el.css('position', 'relative');
                    $(".repl").remove();
                    el.insertAfter(comp_el);
                  });
            break;
        }
    }
}
*/
//function set_activity(el)
//{
//    sendData($(el).attr('href'), {}, function(responce){
//        if(responce.result == 'ok')
//        {
//            $(el)
//            .attr('title', responce.content.title)
//            .text(responce.content.title)
//            .attr('href', $(el).attr('href').slice(0, -2) + responce.content.href+ ' ')
//            .parents('tr')
//            .find('.status_cell')
//            .text(responce.content.cell);
//        }
//    }, 'json');
//    return false;
//}

///** moved to manage.tpl
// * Get tpl html code by id.
// * Source tpl clon with event handlers
// * and return without id attribute
// */
//function getTpl(id)
//{
//    return $('#'+id).clone(true).removeAttr('id');
//}

