/*********************************************
 ***********  ADMIN COMMON TABLE VIEW JS    **********
 ********************************************/
$(function(){
    $('#main_table th :checkbox').live('change', function(){
        $('#main_table td :checkbox').prop('checked', $(this).prop('checked')).eq(0).change();
        
        $(".niceCheck").each(function () {
            changeCheckStart($(this));
        });
    });

    $('#main_table td :checkbox').live('change', function(){
        if( $(":checkbox:checked", '#main_table td').length > 0 )
        {
            showGroupActionButtons();
        }       
        else if($(":checkbox:checked", '#main_table td').length == 0 )
        {
            hideGroupActionButtons();
        }
        if( $(":checkbox:checked", '#main_table td').length == 1 )
        {
            addIdToSingleActionButtons();
            showSingleActionButtons();
        }       
        else if($(":checkbox:checked", '#main_table td').length == 0 
            || $(":checkbox:checked", '#main_table td').length > 1
        ) {
            hideSingleActionButtons();
        }
    });

    if($('#main_table').length > 0)
    {
        $('#main_table tr').hover(
            function(){
                $(this)
                    .data('old_bg_color', $(this).css('background-color'))
                    .css('background-color', '#d0dce8');
            },
            function(){
                $(this)
                    .css('background-color', $(this).data('old_bg_color'));
            }
        );
    }
});

function showGroupActionButtons()
{
    $('.js_group_action:hidden', '.actions').fadeIn('fast');
}

function hideGroupActionButtons()
{
    $('.js_group_action:visible', '.actions').fadeOut('fast');
}

function showSingleActionButtons()
{
    $('.js_single_action:hidden', '.actions').fadeIn('fast');
}

function hideSingleActionButtons()
{
    $('.js_single_action:visible', '.actions').fadeOut('fast');
}

function addIdToSingleActionButtons()
{
    $('.js_single_action', '.actions').each(function(){
        $('a', this).attr('href', 
            $('a', this).attr('href').split('/').slice(0, -1).join('/') 
            + '/' + $(":checkbox:checked", '#main_table td').val() 
        );
    });
}

//  check uncheck all checkboxes
function toggle_all()
{
//    console.log('asd')
//    $('.noform th :checkbox').click(function(){
//        $('.noform td :checkbox').prop('checked', $(this).prop('checked')).eq(0).change();
//    });
    
//    $(":checkbox[name^='item_']").prop('checked', $(":checkbox[name='all_items']").prop('checked')).eq(0).change();
//    $(":checkbox[name^='item_']").attr('checked', $(":checkbox[name='all_items']").is(':checked') ).change();
}

/**
 * delete item responce function complete
 */
function delete_complete(responce)
{
    if(responce.result == 'ok')
    {
        if($("#main_table tr").length - responce.content.length <= 2)
        {
            window.location.reload();
        }
        var ids = [];
        $.each(responce.content, function(){
            ids.push("#line_"+this);
        });
        $(ids.join(', ')).slideUp().remove();
        $('.js_group_action, .js_single_action').hide();
    }
}

/**
 * delete confirmation
 */
function confirm_delete(text)
{
    if(text == undefined)
    {
        text = lang.are_you_sure;
    }
    if(!confirm(text))
    {
        return false;
    }
    return true;
}
/**
 * delete item action
 */
function delete_item(el)
{
    if(!confirm_delete())
    {
        return false;
    }
    sendData($(el).attr('href'), '', delete_complete, 'json');
    return false;
}
/**
 * Delete selected items
 */
function group_delete(el)
{
    if(!confirm(lang.are_you_sure))
    {
        return false;
    }
    var ids = [];
    $(":input[name^='item_']:checked").each(function(){
        ids.push( $(this).val() );
    });
    sendData($(el).attr('href'), {ids: ids}, delete_complete, 'json');
    return false;
}