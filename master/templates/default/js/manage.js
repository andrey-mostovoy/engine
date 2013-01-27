/*********************************************
 ***********  SITE COMMON MANAGE JS    **********
 ********************************************/
$(function(){
});

/**
 * save action
 */
function save()
{
    //console.log($("#manage-save .ckeditor").length);
    //if we have ckeditor content we need update reletive field first
    if($("#manage-save .ckeditor").length > 0)
    {
        for ( instance in CKEDITOR.instances )
        {
            CKEDITOR.instances[instance].updateElement();
        }
    }
    $("form#manage-save").submit();
    return false;
}

/**
 * Get tpl html code by id.
 * Source tpl clon with event handlers
 * and return without id attribute
 */
function getTpl(id)
{
    return $('#'+id).clone(true).removeAttr('id');
}