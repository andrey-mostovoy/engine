/*********************************************
 ***********  ADVIM COMMON MANAGE JS    **********
 ********************************************/
$(function(){
    $("input[type=file]").each(function () {
        $(this).filestyle({
            image: "/templates/default/skins/admin/images/forms/buttons/btn-browse.png",
            imageheight : 28,
            imagewidth : 82,
            width : 250
        });
    });
    
    //date
    if($('#datepicker, #datepicker_from, #datepicker_to').length > 0)
    {
        $('#datepicker, #datepicker_from, #datepicker_to').dateinput({
            format : 'dd/mm/yyyy',
            min: -1
        });
    }
    $('#dialog_link, ul#icons li').each(function () {
        $(this).hover(
            function() {$(this).addClass('ui-state-hover');},
            function() {$(this).removeClass('ui-state-hover');}
        );
    });
    $('#datepicker-icon, #datepicker-icon-from, #datepicker-icon-to').each(function () {
        $(this).datepicker({
            showOn: "button",
            buttonImage: "images/icons/ico-calendar.png",
            buttonImageOnly: true
        });
    });
    // end
});