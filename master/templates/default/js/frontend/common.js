/*********************************************
 ***********  FRONTEND COMMON JS    **********
 ********************************************/
$(function(){
    if($('ul.mmenu').length > 0)
    {
        var curr_marked = $('ul.mmenu li.marked');
        var marker = $('.mmenu-marker div.marker');
        
        function calcMenuLiCenter(li)
        {
            return li.position().left-marker.width()/2+li.outerWidth(true)/2+(li.outerWidth(true)-li.outerWidth())/2;
        }
        
        // version 1
//        $('ul.mmenu')
//        .hover(function(e){
//            curr_marked.toggleClass('marked');
//            marker.toggle();
//        })
//        .mousemove(function(e){
//            marker.stop(true).animate({left:(e.pageX-$(this).offset().left)}, 100);
//        });
        
        //version 2
//        $('ul.mmenu')
//        .hover(function(e){
//            marker.css('left', calcMenuLiCenter(curr_marked));
//            curr_marked.toggleClass('marked');
//            marker.toggle();
//        })
//        .mousemove(function(e){
//            marker.stop(true).animate({left:(e.pageX-$(this).offset().left)}, 100);
//        });

        //version 3
//        $('ul.mmenu')
//        .hover(
//            function(e){
//                marker.css('left', calcMenuLiCenter(curr_marked));
//                curr_marked.toggleClass('marked');
//                marker.toggle();
//            },
//            function(e){
//                marker.stop(true).animate({
//                    left:calcMenuLiCenter(curr_marked)
//                }, 100, function(){
//                    curr_marked.toggleClass('marked');
//                    marker.toggle();
//                });
//            }
//        )
//        .mousemove(function(e){
//            marker.stop(true).animate({left:(e.pageX-$(this).offset().left)}, 150);
//        });

        //version 4
        marker.css('left', calcMenuLiCenter(curr_marked));
        $('ul.mmenu')
        .hover(
            function(e){
            },
            function(e){
                marker.stop(true).animate({
                    left:calcMenuLiCenter(curr_marked)
                }, 100, function(){
                    curr_marked.addClass('marked');
                    marker.hide();
                });
            }
        )
        $('ul.mmenu li')
        .hover(
            function(e){
                curr_marked.removeClass('marked');
                marker.show();
                
                marker.stop(true).animate({
                    left:calcMenuLiCenter($(this))
                }, 100);
            },
            function(e){
            }
        );
    }
    
    // for pages like in admin, i.e. with table view as main view
    // and manage actions
    if(global.action == 'index' && global.controller != 'index')
    {
        //IE table background css correction
        if ($.browser.msie && $.browser.version != '9.0') {
            $(".tbl-listing table tr:nth-child(odd)").css("background","e3e3e3");
        }
    }
});