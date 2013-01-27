/***************************************
 ******      Order JS     **************
 ***************************************/

function order(element)
{
    var elem = $(element);
    var key = elem.attr("id").split("-").pop();
    if(key != undefined)
    {
        var href = elem.attr("href");

        sendData(
            href,
            {__order: key},
            function(result){
                $("#main_table_div").replaceWith(result);
            },
            "text"
        );
    }
    else
    {
        alert("Error Order");
    }
    return false;
}