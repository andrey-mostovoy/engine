/*********************************************
 *************  Dashboadr ADMIN JS    **********
 ********************************************/

function data_import(el)
{
    var parent = $(el).parents('li');
    var btn = parent.html();
    
    sendData(global.url.address+'/importMess', {}, function(r){
        if(r.result == 'ok')
        {
            parent.html(r.content);
            sendData(global.url.address+'/import', {}, function(r){
                if(r.result == 'ok')
                {
                    alert(r.content);
                }
                parent.html(btn);
            }, 'json');
        }
    }, 'json');
    return false;
}