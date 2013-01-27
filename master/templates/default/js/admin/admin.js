//function setTestMail(name)
//{
//    $("#js_test_email").val( $("#js_"+name+"_email").val() );
//    
//    switch(name)
//    {
//        case 'thx':
//            $("#js_test_templ").val( "thx_message" );
//            break;
//        case 'pass':
//            $("#js_test_templ").val( "reset_message" );
//            break;
//        case 'alert':
//            $("#js_test_templ").val( "price_alert_message" );
//            break;
//    }
//    
//    return false;
//}

// removing items like ship/cruise line from amenities or styles
//function remove_item(id, item_id, item_type, link)
//{
//    if(!confirm(lang.are_you_sure))
//    {
//        return false;
//    }
//    else
//    {
//        sendData(link, {'data[id]': id, 'data[item_id]': item_id, 'data[item_type]': item_type}, itemdelete_complete, 'json');
//    }
//}

// function works when deletion items will be completed
//function itemdelete_complete(responce)
//{
//    if(responce.result == 'ok')
//    {
//        window.location.reload();
//    }
//    else
//    {
//        alert(responce.result);
//    }
//}

// adding items like ship/cruise line to amenities or styles
//function add_item(id, item_type, link)
//{    
//    switch (item_type)
//    {
//        case 'ship':
//            var selector = '#js_new_ship';
//        break;
//        case 'cruise':
//            var selector = '#js_new_cruise';
//        break;
//    }
//        
//    if ( $(selector).val() == -1 )
//    {
//        alert("Please choose some item for adding")
//    }
//    else
//    {
//        sendData(link, 
//        {'data[item_id]' : $(selector).val(), 'data[id]' : id, 'data[item_type]' : item_type}, 
//        additem_complete, 'json');
//    }
//}
// function works when adding items will be completed
//function additem_complete(responce)
//{
//    if(responce.result == 'ok')
//    {
//        window.location.reload();
//    }
//    else
//    {
//        alert(responce.result);
//    }
//}