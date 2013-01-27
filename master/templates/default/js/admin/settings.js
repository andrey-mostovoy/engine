function Settings(options)
{
    this.options = {
        'href'      : '/admin/settings/index',
        'group'     : '.group',
        'group_id'  : '#group_',
        'box'       : '.box',
        'box_id'    : '#box_',
        'entry'     : 'entry',
        'action_edit' : '.action_edit',
        'action_delete' : '.action_delete',
        'action_clear' : '.action_clear',
        'action_block' : '.action_block',
        'active_style' : 'background: #ccc'
    }
    
    $.extend(this.options, options);
    
    this.init();
}

Settings.prototype = {
    init : function() {
        var self = this;
        this.group = [];
      
        //parse DOM and form group object
        $(this.get('group')).each(function(){
            var elem = $(this);
            var elemId = elem.attr('id');
            var box = elem.find(self.get('box'));
            var buf = elemId.split('-');
            var parentId = parseInt(buf[2]);
            parentId = parentId ? parentId : 0;
    
            var index = self.group.push({
                'elemId'    : elemId,
                'type'      : buf[1],
                'parent'    : buf[2],
                'entry'     : [],
                'parentId'  : parentId,
                'activeEntry' : null,
                'extraParents' : buf.slice(3) || 0,
                'getEntry'  : function(id){return this.entry[id];}
            });
            
            self.addEntry(elem, self.group[index-1]);
        });
        
        //handle click in group block
        $(this.get('group')).click(function(event){
            self.selectEntry($(event.target), $(this));
            return false;
        });
        
        //handle click in actions block for edit/add actions
        $(this.get('action_edit')).click(function(event){
            event.preventDefault(); 
            self.performAction($(this), 'edit');
            return false;
        });

        $(this.get('action_block')).keypress(function(e) {
            if(e.keyCode == 13) {
                self.performAction($(this).find(self.get('action_edit')), 'edit');
                return false;
            }
        });
        
        //handle clear action
        $(this.get('action_clear')).click(function(){
            self.performClear($(this));
            return false;
        });
        
        //handle delete action
        $(this.get('action_delete')).click(function(){
            self.performAction($(this), 'delete');
            return false;
        }); 
    },
    
    selectEntry : function(elem, group) 
    {
        var group = this.getGroup(group.attr('id'));
        var entry = group.getEntry(elem.attr('id'));
        
        if (!entry) return;
        
        //populate data of selected item to input box
        var inputElem = $('#input-' + group.type).find('input');
        inputElem.val(entry.content);
        inputElem.attr('id', elem.attr('id'));
        
        // manage country flags
        if ($('#set_settings_flag').val() != undefined ) 
        {
            if (group.type == 'country') {
                $('#block_flag').show();
                $('#settings_flag_image').attr('src', global.settings_url+entry.id+'/'+global.settings_size+'.'+global.media_ext);
                $('#country_id').val(entry.id);
            }
            else
            {
                $('#block_flag').hide();
            }
        }
        
        //set active item and style
        this.setActiveEntry(group, entry, true);
        
        //update children
        this.updateChildren(group);
        this.clearGroupChildren(group);
        
        var params = {'id' : entry.id}
        
        var self = this;
       
        sendData(this.get('href'), params, function(ans){
            self.update(ans, group);
        });
    },
    
    update : function(content, invoker)
    {
        var self = this;
        
        if (content.length > 1)
        {
            var content = $(content);
            
            content.each(function(){
                var content = $(this);
                if (!content.attr('id')) return;
                
                var buf = content.attr('id').split('-');
                var group = self.getGroup(buf[1], 'type');
                if (!group) return;
                
                //register new entries
                self.addEntry(content, group);
                
                //clear input box
                self.clearInput(group.type, true);
                
                //update group element
                $('#' + group.elemId).html(content.html());
            });
        }
    },
    
    updateChildren : function (group)
    {
        var children = this.getChildren(group.type);
        
        if (children.length)
        {
            $.each(children, function(key, value) {
                value.parentId = group.activeEntry.id;
            });
        }
    },
    
    addEntry : function (content, group)
    {
        var self = this; 
        var entryList = content.find(self.get('box') + ' div');
        var parentId = group.parentId;
        
        if (!entryList.length)
        {
            entryList = content.find('div');
        }
        entryList.each(function(){
            var elem = $(this);
            var elemId = elem.attr('id');
            var buf = elemId.split('_');
            
            if (!group.entry[elemId]) 
            {   
                group.entry[elemId] = {
                    'elemId'    : elemId,
                    'id'        : buf[1],
                    'parent'    : buf[2],
                    'content'   : elem.html()
                };
            }
            parentId = buf[2];
        }); 
        if (parentId)
        {
            group.parentId = parentId;
        }
    },
    
    setActiveEntry : function(group, entry, isEdit)
    {
        group.activeEntry = entry;
        $('#' + group.elemId + ' div').removeClass('active');
        $('#' + group.elemId + ' div').removeClass('edit');
        $('#' + entry.elemId).addClass('active');
        
        if (isEdit)
        {
            $('#' + entry.elemId).addClass('edit');
        }
    },
    
    performAction : function(elem, action)
    {
        var href = elem.attr('href'); 
        var actionBlock = elem.parents('.action_block');
        var type = actionBlock.attr('id').split('-')[1];
        var inputElem = actionBlock.find('input');
        var group = this.getGroup(type, 'type');
        var entry = group.getEntry(inputElem.attr('id'));

        if (!inputElem.val() || !group.parentId) return;
        
        var params = {
            'href'  : href,
            'group' : group,
            'entry' : entry,
            'inputElem' : inputElem
        }
        
        if ('edit' == action)
        {
            this.performEdit(params);
        }
        else
        {
            this.performDelete(params);
        }
    },
    
    performEdit : function (params)
    {
        var entryId = params.entry ? params.entry.id : 0;
        
        var request = {
            'data' : {
                'id'     : entryId,
                'name'   : params.inputElem.val(),
                'parent' : params.group.parentId,
                'type'   : params.group.type
            }
        }

        var self = this;
        sendData(params.href, request,  function(ans){
            if (ans.length > 1 && entryId)
            {
                $('#' + params.entry.elemId).html($(ans).html());
                params.entry.content = $(ans).find('div').html();
                self.clearInput(params.group.type, true);
            }
            else if (ans.length > 1)
            {
                var elem = $(ans);
                var entry = elem.find('div');
                var buf = entry.attr('id').split('_');
                
                self.addEntry(elem, params.group);
                
                var box = $('#' + params.group.elemId + ' .box');
                
                if (box.length)
                {
                    box.append(elem.html());
                }
                else
                {
                    $('#' + params.group.elemId).append(elem);
                }
                self.clearInput(params.group.type, true);
            }
        });
    },
    
    performDelete : function (params)
    {
        var entryId = params.entry ? params.entry.id : 0;
        
        var request = {
            'id' : entryId
        }
        var self = this;
        sendData(params.href, request, function() {
            self.clearInput(params.group.type, true);
            $('#' + params.entry.elemId).remove();
            params.entry = null;
            self.clearGroupChildren(params.group);
        });
    },
    
    performClear : function (elem)
    {
        this.clearInput(elem);
    },
    
    clearInput : function (elem, isGroup)
    {
        if (isGroup)
        {
            var inputElem = $('#input-' + elem).find('input');
        }
        else
        {
            var inputElem = elem.parents('.settings-actions').find('input');
        }
        
        var id = inputElem.attr('id');
        inputElem.val('');
        inputElem.attr('id', '');
        if (id) 
        {
            $('#' + id).removeClass('edit');
        }
    },
    
    clearGroupChildren : function (group, needRemove)
    {
        var children = this.getChildren(group.type, true);
        
        if (children)
        {
            var self = this;
            $.each(children, function(key, value){
                self.clearInput(value.type, true);
                $('#' + value.elemId + ' .box').empty();
                self.clearGroupChildren(value);
            });
        }
    },
    
    getGroup : function (search, param)
    {
        var result = null;
        var param = param || 'elemId';
        
        $.each(this.group, function(id, group){
            if (group[param] == search)
            {
                result = group;
            }
        });
        
        return result;
    },
    
    getChildren : function (type, notAll)
    {
        var result = [];
        
        var self = this;
        $.each(this.group, function(id, group){
            if (group.parent == type)
            {
                result.push(group);
            }
            else if (!notAll && group.extraParents.length)
            {
                $.each(group.extraParents, function(key, value){
                    if (type == value)
                    {
                        result.push(group);
                    }
                });
            }
        });

        return result;
    },
    
    splitData : function (data)
    {
        return data.split('_') || [];
    },
    
    get : function(name)
    {
        return this.options[name];
    }
}