
(function($) {

    // default options
    var defaults = {
        'block'         : 'div.js-ajax-block', // selector for ajax messages block
        'text_conteiner': '.text-content',  // selector for text content element
        'message_wrap'  : 'p',              // tag for wrap text message
        'hidden'        : true,             // current hidden status
        'hidden_class'  : 'hidden',
        'error_mes_class': 'ajax_error',
        'message_class' : 'ajax_message',
        'method'        : 'replace',
        'after'         : null
    };
    // object with cur options
    var options = $.extend({can_hide: true, can_scroll:true}, defaults);

    // public ajaxer methods
    var methods = {
        // plugin initializing
        init : function(params) {            
            options = $.extend({}, options, defaults, params);
            
            p_methods.hideable();

            this.data('ajaxer', options);
            this.live('click.ajaxer', methods.sendAjax);
            
            return this;
        },
        
        bindGlobalAjaxEvents : function(){
            
            $.ajaxSetup({
//                dataType  : 'json',
                type : 'POST'
            });
            
            $(document).ajaxStart(function(){
                box_methods.setText('Loading ...').show();
            });
            $(document).ajaxSend(function(evt, request, settings){
                if(settings.data == undefined || settings.data == '')
                {
                    settings.url += '/__a/1';
                }
                else if(settings.data.search('__a') == -1)
                {
                    settings.data += '&__a=1';
                }
            });
            $(document).ajaxSuccess(function(evt, request, settings){
//                if(request.on_success != undefined)
//                    methods.ajaxResponse(request.on_success.resp, request.on_success.func);
                box_methods.setText('').hide();
            });
            $(document).ajaxError(function(e, jqxhr, settings, exception){
                if(typeof console !== undefined && global.debug)
                {
                    console.log(e);
                    console.log(jqxhr);
                    console.log(settings);
                    console.log(exception);
                    box_methods.setError(exception.name+': '+exception.message).show();
                }
            });
            $(document).ajaxComplete(function(event,request, settings){
            });
            $(document).ajaxStop(function(){
//                box_methods.setText('').hide();
            });
        },
        
        /**
         * Sends request
         */
        sendAjax : function()
        {
            var elem = $(this);
            var href = elem.attr('href');
            if (!href || '#' == href || elem.data('ajaxer_clicked')) return false;
            elem.data('ajaxer_clicked',true);
            var opt = elem.data('ajaxer');
            
            methods.sendData(href, undefined, function(ans) {
                if (ans.result == 'html')
                {
                    methods[opt.method]($(ans.content));
                }
            }, 'json', 'get');
            
            return false;
        },

        /**
         * Finds content with the same id and replace them
         * 
         * @param {jQuery} content
         */
        replace : function(content)
        {
            var id = content.attr('id');
            if (!id) return;

            var element = $('#' + id);
            if (!element.length) return;

            // if replace content with ajax append handlers, rebind them
            if ($('.js_ajax_append', content).length > 0)
            {
                $('.js_ajax_append', content).ajaxer({
                    'method' : 'append',
                    'after'  : options.after
                });
            }

            content.find('a.js_ajax').data('ajaxer', $('#' + id).find('a.js_ajax').data('ajaxer'));
            $('#' + id).replaceWith(content);
            
            if ($.isFunction(options.after))
            {
                options.after(content);
            }
            
//            if(options.can_scroll)
//                $.scrollTo($('#' + id), 600, {offset: {top:-50}} );
        },
        
        /**
         * Finds content with the same id and add to them own content
         * 
         * @param {jQuery} content
         */
        append : function(content)
        {
            var id = content.attr('id');
            if (!id) return;

            var element = $('#' + id);
            if (!element.length) return;

            var to_scroll = $('#' + id).find('>li:last,>div:last,>tr:last').eq(0);
            
            $('#' + id).append(content.html());

            content.each(function(){
                if($(this).hasClass('js_replace_after'))
                {
                    p_methods.scrollable(false);
                    methods.replace($(this));
                    p_methods.scrollable(true);
                }
            });
            
            
            if ($.isFunction(options.after))
            {
                options.after(content);
            }
            
            if(options.can_scroll)
                $.scrollTo(to_scroll, 600, {offset: {top:-50}} );
        },
        
        // send post @data by @url by ajax and delegate to @success function
        // if @success function not set ajaxSuccess function used
        // if @success function not implemented standart flow used
        // To use standart flow after @success function need return true in function
        sendData : function(url, data, success, dataType, type)
        {
            if (success == undefined) {
                success = ajaxSuccess;
            }
            
            p_methods.hideable();
            
            switch (dataType) {
                case 'json' :
                case 'script' :
                case 'xml' :
                    break;
                default :
                    dataType = "text";
                    break;
            }

            switch(type){
                case 'post':
                case 'get':
                    break;
                default :
                    type = 'post';
                    break;
            }
            $.ajax({
                url       : url,
                type      : type,
                dataType  : dataType,
                data      : data,
                success   : function (response) {
                    methods.ajaxResponse(response, success);
                }
            });
        },
        
        // common handle ajax response
        // if callback function return true this launch default common 
        // actions, if false - not
        // response consist from result and content. Result can be next:
        //  - ok        - all good. Standart content format: custom.
        //  - error     - some errors. Standart content format: string.
        //  - message   - some message. Standart content format: string.
        //  - redirect  - redirect browser. Standart content format: url string.
        //  - confirm   - need confirm from user. Standart content format: array(message=>text,func=>funcName).
        //  - html      - need insert some html to somewhere. Standart content format: custom.
        // Content consist from some content
        ajaxResponse : function(response, success)
        {
            if(response != undefined && response.result == 'redirect')
            {
                window.location.href = response.content;
                return;
            }
            var a = true; 
            if( $.isFunction(success) )
            {
                a = a && success(response);
            }
            if(a)
            {
                switch(response.result)
                {
                    case 'error':
                    case 'message':
                        box_methods.setError(response.content).show();
                    break;
                    case 'confirm':
                        if( confirm(response.content.message) )
                        {
                            if(response.content.func != undefined)
                            {
                                eval( "if($.isFunction("+response.content.func+")) { "+response.content.func+"(); }" );
                            }
                        }
                        else
                        {

                        }
                    break;
                }
            }
        }
    }
    
    var box_methods = {
        setText : function(t)
        {
            if (t === '' && options.can_hide === false)
            {
                return this;
            }

            $(options.block+' '+options.text_conteiner)
                .html('<'+options.message_wrap+'>').find(options.message_wrap).text(t);
            box_methods.show();
            return this;
        },
        setHtml : function(h){
            if (h === '' && options.can_hide === false)
            {
                return this;
            }
            $(options.block+' '+options.text_conteiner)
                .html(h);
            return this;
        },
        setMultiText    : function(mt){

        },
        setMultiHtml    : function(mh){

        },
        setError : function(t){
            box_methods.setText(t).addErrorClass().show();
            return this;
        },
        setMessage : function(t){
            box_methods.setText(t).addMessageClass().show();
            return this;
        },
        show : function(){
            $(options.block).removeClass(options.hidden_class);
            return this;
        },
        hide : function(){
            if(options.can_hide == true)
                $(options.block).addClass(options.hidden_class);
            return this;
        },
        closeMessage : function(){
            p_methods.hideable();
            box_methods.setText('').hide();
        },
        addErrorClass : function(){
            p_methods.hideable(false);
            $(options.block+' '+options.text_conteiner)
                .find(options.message_wrap).last().addClass(options.error_mes_class);
            return this;
        },
        addMessageClass : function(){
            p_methods.hideable(false);
            $(options.block+' '+options.text_conteiner)
                .find(options.message_wrap).last().addClass(options.message_class);
            return this;
        }
    }
    
    var p_methods = {
        hideable : function(v){
            if(v == undefined)
                v = true;
            options.can_hide = v;
        },
        scrollable : function(v) {
            if(v == undefined)
                v = true;
            options.can_scroll = v;
        }
    }
    
    // jQuery plugin initialization
	$.ajaxbox = $.ajaxer = $.fn.ajaxer = function(method) {
        if ( methods[method] ) {
            // if method is present we call it
            // all arguments will be passed to the method
            // this also passed to method
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( box_methods[method] ) {
            return box_methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            // if first parametr - object or just empty
            // execute init method
            return methods.init.apply( this, arguments );
        } else {
            // if nothing - error
            $.error( 'Method "' +  method + '" not found in plugin' );
        }
    }
})(jQuery);

$(document).ready(function(){
    $.ajaxer('bindGlobalAjaxEvents');
});