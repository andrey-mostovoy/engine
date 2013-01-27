/*********************************************
 ***********  FRONTEND CV MANAGE JS    **********
 ********************************************/
$(function(){
    $(".slider-content").scrollable();
//    $(".slider-content").scrollableAddClones();
    initDateinput($(".date-row input"));
    
    var nums = {
        'education' : global.cv_nums.education,
        'work' : global.cv_nums.work,
        'candc' : global.cv_nums.candc,
        'language' : global.cv_nums.language
    };
    $('.mult_it .add_more a').click(function(){
        var tpl_id = $(this).parents('.mult_it').attr('id');
        var tpl = getTpl('tpl_'+tpl_id);
        
        tpl.html(tpl.html().replace(/__num__/g, nums[tpl_id]++));
        
        initDateinput($(".date-row input",tpl));
        $(this).parents('.mult_it').find('.items-content').append(tpl);
        repositionValidationErrors();
        return false;
    });
    
    $('body').on("click", '.mult_it .del_more_one a', function(){
        var parent = $(this).parents('.item').eq(0);
        if(parent.find(':input[name$="][id]"]').length > 0)
        {
            sendData(global.url.address+'/deleteitem',
                {
                    item: parent.parents('.mult_it').attr('id'),
                    item_id: parent.find(':input[name$="][id]"]').val()
                },
                function(r){
                    if(r.result == 'ok')
                    {
                        parent.remove();
                    }
                }, 'json');
        }
        else
        {
            parent.remove();
        }
        return false;
    });
    
    $(".slider-content a.choice").click(function(){
        $(':input[name="__data[cv_info][template_id]"]').val($('span',this).text());
        return false;
    });
});

function initDateinput(el)
{
    el.dateinput({
        format: 'yyyy/mm/dd',
        trigger: true,
        yearRange: [-50,2],
        selectors: true
    });
}