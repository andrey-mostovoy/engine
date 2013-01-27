/*
* Library just show and hide some div on page. Can be modal or not.
* Can be modified few setting such as speed of show, hide and scroll.
* Can peak popup position
*       LT    T   RT
*       L     C   R
*       LB    B   RB
* Can call user function before and after show, before and after hide.
* 
* @author Mostovoy A.V.
* @version 1.3
* @example
* 
* script call examples
* <script type="text/javascript">
	<!--
		function showpp()
		{ // default popup show
			$("#pp").showPopup();
		// we can give some options
//			$("#pp").showPopup({modal:false});
		}

		function hidepp()
		{ // default popup hide
			$("#pp").hidePopup();
		}

		// assign to images in #gal block posibility to show img in rel attribute in popup.
		// if no rel attr will be placed image from src attr
		$(document).ready(function(){
			$("#gal img").imgPopup();
		});
	//-->
	</script>
*
* 2010
*/

(function($) {
	// default configuration properties
	var defaults = {
		modal:				true,	// show background or not
		modal_bg_color:		'black',// background color
        modal_bg_opacity:   0.8,    // modal background opacity
        bg_class:           'popup-modal-blackout',
        fixed_size:         true,   // fix size of element, i.e. not resize element to fit to window
		position:			'C',	// can be C - center, L - left, R - right, T-top, B-bottom, LT-left top, LB, RT, RB
        margin:             '0 0',  // margins in position: top-left
		show_speed:			500,    // show animation speed
		hide_speed:			100,    // hide animation speed
        scrolling:          false,   // enable scrolling element with document scroll
        scroll_speed:		100,    // scroll animation speed
		zindex:				666,   // popup zindex
        wrap:               false,   // flag to use wrap popup to create special style of popup
        page_wrap:          '#s-wrap',// page wrap selector
        debug:              false,  // debug mod
        popup_wrap_class:   'l-popup-wrap',
		before_show:		false,	// this is user function name to do before show popup
		after_show:			false,  // this is user function name to do after show popup
		before_hide:		false,  // this is user function name to do before hide popup
		after_hide:			false   // this is user function name to do after show popup
	};
    var els = [];
	var dobj = {
			el: '',
			pos: [],
            noScrollUp: false,
            noScrollDown: false,
			visible: false,
			modalBlackout: false,
			wraped: false,
			modalBgPos: [],
			isImage:false,
			imgDiv: false,
			imgDivId: 'popup-img-holder'
		};
    var page_wrap = {
        el: false,
        id: '',
        css: {
            old: {},
            cur: {}
        }
    };
    var popup_wrap = {
        el: false,
        _class: ''
    };
    var bg_wrap = {
        el: false,
        _class: ''
    };
    var wraped = false;
    var obj;
    var count = 0;
	var scrollPos = [];
	var windowSize = [];
	var documentSize = [];

    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };
            
	$.fn.showPopup = function(options){
        
		var options = $.extend({}, defaults, options);
        
        popup_wrap._class = options.popup_wrap_class;
        page_wrap.id = options.page_wrap;
        bg_wrap._class = options.bg_class;
        
        if(options.wrap && wraped)
            options.wrap = false;
        
        debug = function(t){
            if(options.debug)
                console.log(t);
        };
        
        debug('init show popup');
        
		show = function() {
            debug('start show');
            
			if (options.before_show) {
                debug('before show');
				options.before_show();
            }
            
            if (obj.visible) return;
            
            if(!page_wrap.el)
                page_wrap.el = $(options.page_wrap);

			if ( $(obj.el).get(0).tagName.toLowerCase() == 'img' )
			{
				obj.isImage = true;
				obj.imgDiv = $('<div id="'+obj.imgDivId+'"></div>').appendTo('body');
				$("#"+obj.imgDivId).html('<img src="'+($(obj.el).attr('rel')?$(obj.el).attr('rel'):$(obj.el).attr('src'))+'"/>');
				obj.el = $("#"+obj.imgDivId);
                var img = obj.el.find('img');
                obj.el.width( img.width() ).height( img.height() );
                var el = obj.el;
                img.bind("load",function(){
                    el.width( $(this).width() ).height( $(this).height() );
                    moveToPosition();
                });
			}

            showBackground();

			bindGlobalEvents();

            getPosition();

            showElement();

			if (options.after_show) {
                debug('after show');
				options.after_show();
            }

            $(obj.el).data('popup', {'options':options});
            
            debug('end show');
		};
        
        showElement = function()
        {
            debug('show popup');
            
            if(options.wrap && !wraped)
            {
                var el = obj.el;
                popup_wrap.el = jQuery('<div class="'+popup_wrap._class+'"></div>')
						.css({
                            zIndex: options.zindex+Object.size(els),
                            display: 'block',
							position: 'fixed',
							left: 0,
							top: 0,
							width: '100%',
							height: '100%',
                            'overflow-x': 'hidden',
                            'overflow-y': 'auto'
                        })
                        .bind("click", function(e){
                            if($(e.target).attr('class') == popup_wrap._class)
                                $(el).hidePopup();
						});
                resizeDocumentHeight();
            }
            
            var css_map = {};
            
            if(options.wrap && !wraped)
            {
                css_map = {
                        'opacity': 0,
                        'position': 'absolute',
                        'display': 'block',
                        'overflow': 'visible',
                        'z-index': 'auto',
                        'top': obj.pos['top'] < 0 ? 0 : obj.pos['top'],
                        'left': obj.pos['left']
                    };
            }
            else
            {
                css_map = {
                        'opacity': 0,
                        'position': 'absolute',
						'z-index': options.zindex+Object.size(els),
                        'top': obj.pos['top'],
                        'left': obj.pos['left']
                    };
            }
                
			obj.el.stop()
					.css(css_map)
					.show()
					.animate({opacity: 1}, options.show_speed, function() {
						obj.el.show();
						obj.visible = true;
                        count++;
                        if(options.wrap && !wraped)
                        {
                            obj.el
                                .wrap(popup_wrap.el);
                            obj.wraped = true;
                            wraped = true;
                            popup_wrap.el = $('body').find('.'+popup_wrap._class).eq(0);
                        }
            
                        if (obj.isImage)
                        {
                            moveToPosition();
                        }
					})
                    .focus();
        };
        
        showBackground = function()
        {
            var css_map = [];

			if (options.modal)
			{
                debug('init modal');
				if ( $('body').find('.'+bg_wrap._class).length == 0 )
				{
                    debug('modal background');
					getModalBgPos();
                    var el = obj.el;
                    if(options.wrap)
                    {
                        css_map = {
                            zIndex: options.zindex-2,
                            opacity: 0,
                            display: 'block',
                            position: 'fixed',
                            left: 0,
                            top: 0,
                            width: '100%',
                            height: '100%',
                            overflow: 'hidden',
                            backgroundColor: options.modal_bg_color
                        };
                    }
                    else
                    {
                        css_map = {
                            zIndex: options.zindex-2,
                            opacity: 0,                      
                            position: 'absolute',
                            left: obj.modalBgPos['left'],
                            top: obj.modalBgPos['top'],
                            width: documentSize['width'],
                            height: documentSize['height'],
                            backgroundColor: options.modal_bg_color
                        };
                    }

					obj.modalBlackout = jQuery('<div class="'+bg_wrap._class+'"></div>')
						.css(css_map)
						.appendTo('body')
						.bind("click", function(){
                            $(el).hidePopup();
						})
						.animate({opacity: options.modal_bg_opacity}, options.show_speed);
				}
			}
        };

        resizeDocumentHeight = function(){
            // save old page wrap element css style
            if(!Object.size(page_wrap.css.old))
            {
                page_wrap.css.old = {
                    'height': page_wrap.el.height(),
                    'width': page_wrap.el.width(),
                    'margin-top': page_wrap.el.css('margin-top'),
                    'margin-bottom': page_wrap.el.css('margin-bottom'),
                    'padding-top': page_wrap.el.css('padding-top'),
                    'padding-bottom': page_wrap.el.css('padding-bottom'),
                    'overflow': page_wrap.el.css('overflow'),
                    'background': page_wrap.el.css('background')
                };
            }
            
            // create new style to page wrap element
            if(!Object.size(page_wrap.css.cur))
            {
                page_wrap.css.cur = {
                    'height': windowSize['height']+scrollPos['top'],
                    'width': parseInt(page_wrap.el.width()) + (documentSize['height'] > windowSize['height'] ? 0 : window.scrollBarWidth()),
                    'margin-top': -scrollPos['top'],
                    'margin-bottom': 0,
                    'padding-top': 0,
                    'padding-bottom': 0,
                    'overflow': 'hidden',
                    'background': 'none'
                };
            }
            
//            $('body').css({overflow:'hidden'});
            
            page_wrap.el.css(page_wrap.css.cur);
        };
        
        window.scrollBarWidth = function() { 
            document.body.style.overflow = 'hidden';  
            var width = document.body.clientWidth; 
            document.body.style.overflow = 'scroll';  
            width -= document.body.clientWidth;  
            if(!width) width = document.body.offsetWidth - document.body.clientWidth; 
            document.body.style.overflow = '';  
            return width;  
        };

		moveToPosition = function(){
            
            scrolling();
            
		};

		scrolling = function(){
            if(options.scrolling)
            {
                if (!obj.visible) return;

                getPosition();

                if (options.modal) {
//                    moveBg();
                }
                obj.el.stop()
                    .animate({'top': obj.pos['top'],
                            'left': obj.pos['left']},
                            options.scroll_speed);
            }
		};

		getPosition = function(){
            debug('start get positions');
            
			getWindowSize();
			getScrollPos();

            fitToScreen();
            
            switch(options.position)
            {
                case 'C':	// center
                default:
                    getCenterPos();
                    break;
                case 'L':	// left
                    getLeftPos();
                    break;
                case 'R':	// right
                    getRightPos();
                    break;
                case 'T':	// top
                    getTopPos();
                    break;
                case 'B':	// bottom
                    getBottomPos();
                    break;
                case 'LT':	// left top
                    getLeftTopPos();
                    break;
                case 'LB':	// left bottom
                    getLeftBottomPos();
                    break;
                case 'RT':	// right top
                    getRightTopPos();
                    break;
                case 'RB':	// right bottom
                    getRightBottomPos();
                    break;
            }
            
            fixPosition();
            
            debug('end get positions');
		};
        
        fixPosition = function(){
            addMargin();

            if((obj.pos['top'] + obj.el.height()) > documentSize['height'])
            {
                obj.pos['top'] = documentSize['height']-obj.el.height();
            }
            if((obj.pos['left'] + obj.el.width()) > documentSize['width'])
            {
                obj.pos['left'] = documentSize['width']-obj.el.width();
            }
        };
        
        addMargin = function(){
            if(typeof( options.margin ) != 'object')
            {
                options.margin = options.margin.split(' ');
            }
            obj.pos['top'] += parseInt(options.margin[0]);
            obj.pos['left'] += parseInt(options.margin[1]);
        };
        
		getCenterPos = function(){
            if(options.wrap)
            {
                obj.pos['top'] = Math.floor((windowSize['height'] - obj.el.height())/2);
            }
            else
            {
                if(count > 0 && wraped)
                {

                    obj.pos['top'] = Math.floor((windowSize['height'] - obj.el.height())/2 + Math.abs(parseInt(page_wrap.el.css('margin-top'))));

                }
                else
                    obj.pos['top'] = scrollPos['top'] + ( Math.floor((windowSize['height'] - obj.el.height())/2) );
            }
			obj.pos['left'] = ( windowSize['width']/2 ) + scrollPos['left'] - (obj.el.width()/2);
		};

		getLeftPos = function(){
			obj.pos['top'] = scrollPos['top'] + ( (windowSize['height']/2) - (obj.el.height()/2) );
			obj.pos['left'] = scrollPos['left'];
		};

		getRightPos = function(){
			obj.pos['top'] = scrollPos['top'] + ( (windowSize['height']/2) - (obj.el.height()/2) );
			obj.pos['left'] = ( windowSize['width'] ) + scrollPos['left'] - ( obj.el.width() );
		};

		getTopPos = function(){
            if(options.wrap)
            {
                obj.pos['top'] = 0;
            }
            else
            {
                obj.pos['top'] = scrollPos['top'] + Math.abs(parseInt(page_wrap.el.css('margin-top')));
            }
			obj.pos['left'] = ( windowSize['width']/2 ) + scrollPos['left'] - (obj.el.width()/2);
		};

		getBottomPos = function(){
			obj.pos['top'] = scrollPos['top'] + ( (windowSize['height']) - (obj.el.height()) );
			obj.pos['left'] = ( windowSize['width']/2 ) + scrollPos['left'] - (obj.el.width()/2);
		};

		getLeftTopPos = function(){
			obj.pos['top'] = scrollPos['top'];
			obj.pos['left'] = scrollPos['left'];
		};

		getLeftBottomPos = function(){
			obj.pos['top'] = scrollPos['top'] + ( (windowSize['height']) - (obj.el.height()) );
			obj.pos['left'] = scrollPos['left'];
		};

		getRightTopPos = function(){
			obj.pos['top'] = scrollPos['top'];
			obj.pos['left'] = ( windowSize['width'] ) + scrollPos['left'] - (obj.el.width());
		};

		getRightBottomPos = function(){
			obj.pos['top'] = scrollPos['top'] + ( (windowSize['height']) - (obj.el.height()) );
			obj.pos['left'] = ( windowSize['width'] ) + scrollPos['left'] - (obj.el.width());
		};

		getScrollPos = function(){
			scrollPos['top'] = $(window).scrollTop();
			scrollPos['left'] = $(window).scrollLeft();
		};

		getWindowSize = function(){
			windowSize['height'] = $(window).height();
			windowSize['width'] = $(window).width();
		};

		getDocumentSize = function(){
			documentSize['height'] = $(document).height();
			documentSize['width'] = $(document).width();
		};

		getModalBgPos = function(){
			getDocumentSize();
			obj.modalBgPos['top'] = 0;//scrollPos['top'];
			obj.modalBgPos['left'] = 0;//scrollPos['left'];
		};

		moveBg = function(){
			getModalBgPos();

            if(obj.modalBlackout)
                obj.modalBlackout.stop()
                    .css({left: obj.modalBgPos['left'],
                            top: obj.modalBgPos['top'],
                            width: documentSize['width'],
                            height: documentSize['height']
                        });
		};

        isScrolledIntoView = function()
        {
            var docViewTop = $(window).scrollTop(); 
            var docViewBottom = docViewTop + windowSize['height']; 
            var elemTop = obj.el.offset().top; 
            var elemBottom = elemTop + obj.el.height();
            return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom)); 
        };

        // resize element to fit window size
		fitToScreen = function()
		{
            if(!options.fixed_size)
            {
                if ( windowSize['width'] < obj.el.width() )
                {
                    obj.el.width( windowSize['width'] - 20 );
                    obj.el.height( obj.el.height() + 15 );
                    obj.el.css('overflow', 'auto');
                }
                if ( windowSize['height'] < obj.el.height() )
                {
                    obj.el.height( windowSize['height'] - 20 );
                    obj.el.width( obj.el.width() + 15 );
                    obj.el.css('overflow', 'auto');
                }
            }
		};

        fixToPosition = function()
        {
            if(scrollPos['top'] < (obj.pos['top']-30))
            {
                obj.noScrollUp = true;
                obj.noScrollDown = false;
                return false;
            }
            if((scrollPos['top']+obj.el.height()) > (obj.pos['top']+obj.el.height()+30))
            {
                obj.noScrollUp = false;
                obj.noScrollDown = true;
                return false;
            }
            return true;
        };
        
        onResize = function()
        {
            if(options.wrap)
            {
                getWindowSize();
                
//                page_wrap.css.cur.height = windowSize['height'];
                page_wrap.css.cur.height = Math.abs(parseInt(page_wrap.el.css('margin-top')))+windowSize['height'];
                
                resizeDocumentHeight();
            }
        };
        
		bindGlobalEvents = function()
		{
            debug('start bind events');
            
            var o = obj;
			$(document).keydown(function(e){
				if ( o.visible )
					switch(e.keyCode){
						case 27:	// ESC
							$(obj.el).hidePopup();
						break;
//                        case 33:case 36:case 38:    // scroll up
//                            if(obj.noScrollUp)
//                            {
//                                e.preventDefault();
//                                return false;
//                            }
//                        break;
//                        case 34:case 35:case 40:    // scroll down
//                            if(obj.noScrollDown)
//                            {
//                                e.preventDefault();
//                                return false;
//                            }
                        break;
					};
			});
            
            $(window).scroll(function(e) {
                if(options.scrolling)
                {
                    getWindowSize();
                    getScrollPos();
                    
//                    fixToPosition();
                    moveToPosition();
                }
            }).resize(function(){
                onResize();
                moveToPosition();
            });
            
            debug('end bind events');
		};

        formIdentify = function(e)
        {
            var i = e.nodeName.toLowerCase();
            if($(e).attr('id'))
                i += '#'+ $(e).attr('id');
            if($(e).attr('class'))
                i += '.'+ $(e).attr('class').replace(' ', '');
            return i;
        };
        
        clone = function(o) {
            if(!o || 'object' !== typeof o)  {
                return o;
            }
            var c = 'function' === typeof o.pop ? [] : {};
            var p, v;
            for(p in o) {
                if(o.hasOwnProperty(p)) {
                    v = o[p];
                    if(v && 'object' === typeof v) {
                        c[p] = clone(v);
                    }
                    else {
                        c[p] = v;
                    }
                }
            }
            return c;
        };

		return this.each(function () {
			// plugin code action

            var e = formIdentify(this);

            if(els[e] == undefined)
            {
                obj = clone(dobj);
                obj.el = $(this);
                els[e] = obj;
            }
            
			obj = els[e];

            try{
                show();
            } catch(exc){}
			
			return this;
		});
	};

	$.fn.hidePopup = function(options){

		var options = $.extend({}, defaults, options);

        restoreDocumentSize = function()
        {
            page_wrap.el.css(page_wrap.css.old);
            
            backToPosition();

            page_wrap.css.old = {};
            page_wrap.css.cur = {};
        };
        
        backToPosition = function()
        {
//            $('body').css({overflow:''});
            window.scroll(0, Math.abs(parseInt(page_wrap.css.cur['margin-top'])));
        };

		hide = function (){
			if (!obj.visible) return;
			obj.visible = false;

			if (options.before_hide)
				options.before_hide();

			if (options.modal && obj.modalBlackout !== false) {
				obj.modalBlackout.unbind("click").animate({opacity: 0}, options.hide_speed, function() {
					jQuery(obj.modalBlackout).remove();
				});
			}
                
			obj.el.stop().animate({opacity: 0}, options.hide_speed, function() {
				obj.el.hide();

                count--;
                if(obj.wraped)
                {
                    obj.el.unwrap();
                    restoreDocumentSize();
                    obj.wraped = false;
                    wraped = false;
                    popup_wrap.el = false;
                }
                
				obj.visible = false;
			});

			if (obj.isImage)
			{
				$(obj.el).remove();
				obj.isImage = false;
			}

			if (options.after_hide)
				options.after_hide();
		};

		return this.each(function () {
			// plugin code action
            var e = formIdentify(this);

            if(els[e] == undefined)
            {
                obj = clone(dobj);
                obj.el = $(this);
                els[e] = obj;
            }
            
			obj = els[e];
            
            try{
                hide();
            } catch (exc){}
            
			return this;
		});
	};

	$.fn.moveToPosition = function(options){
		var options = $.extend({}, defaults, options);

		return this.each(function () {
			// plugin code action
			moveToPosition();
			return this;
		});
	};

	$.fn.imgPopup = function(options){

		var options = $.extend({}, defaults, options);

		return this.each(function () {
			// plugin code action

			$(this).each(function(){
				$(this).bind("click", function(){
					$(this).showPopup();
				});
			});
			return this;
		});
	};
})(jQuery);