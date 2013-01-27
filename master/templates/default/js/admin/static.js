/*********************************************
 *************  Static ADMIN JS    **********
 ********************************************/

$(document).ready(function(){
    $(".tabs ul").tabs(".pane-tab", {
        initialIndex: (global.active_type != undefined ? global.active_type : 0),
        effect: 'ajax',
//        history: true,
        current: 'selected',
        onBeforeClick: function(event, i) {
            this.getCurrentTab()
                .parents('li').eq(0)
                .removeClass(this.getConf().current);
		},
        onClick: function(event, i) {
            this.getCurrentTab()
                .removeClass(this.getConf().current)
                .parents('li').eq(0)
                .addClass(this.getConf().current);
                
            // set breadcrumb
            var txt = this.getCurrentTab().text();
            var href = this.getCurrentTab().attr('href');
            if( $('.title-container ul li').length > 1 && txt != '')
            {
                $('.title-container ul li:eq(1)>a')
                    .text(txt).attr('title',txt).attr('href',href);
            }
            else
            {
                var cl = $('.title-container ul li:last').clone();
                cl.find('a').text(txt).attr('title',txt).attr('href',href);
                $('.title-container ul').append(cl);
            }
		}
    });
});