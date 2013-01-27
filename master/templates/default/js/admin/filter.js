/**********************************************/
/****************FILTER JS********************/
/********************************************/

var filter_elemnts = [];
$(document).ready(function(){

    filter_default();

    $("form#filter-apply input").live('blur', changeFilter);
    $("form#filter-apply input").live('focus', changeFilter);
    $("form#filter-apply").live('keyup', function(event){
        if(event.keyCode == 13)
        {
             filter();
        }
        return false;
    });
    $("#filter-apply").submit(function(){
        filter();
        return false;
    });
});

/**
 *
 */
function filter()
{
    clear_filter_form();
    
    var form = $("form#filter-apply");
    
    var data = form.serialize();
    var href = form.attr("action");

    submit_filter(data,href,true);
    
    return false;
}
function resetForm()
{ 
      var form = $("form#filter-apply input[type='text']").get();
      for(var i = 0; i < form.length; i++)
      {
          var elem = $(form[i]);
          var val = elem.val();
          var key = elem.attr('id').split('-');
          key = key[0];
          filter_elemnts[key] = trim(filter_elemnts[key]);
          elem.val(filter_elemnts[key]);
      }
     
      var select = $("form#filter-apply select").get();
      var test = '';
      for(var i = 0; i < select.length; i++)
      {
             var elem = $(select[i]);
             var id = elem.attr("id");
             if (id)
             {
                $("#"+id+" :first").attr("selected","selected");
             }
      }
}
function trim(sInString)
{

        sInString = sInString.replace(/ /g,' ');
        return sInString.replace(/(^\s+)|(\s+$)/g, "");

}
function default_form()
{
      var form = $("form#filter-apply input[type='text']").get();
      for(var i = 0; i < form.length; i++)
      {
          var elem = $(form[i]);
          var val = elem.val();
          var key = elem.attr('id').split('-');
          key = key[0];
          val = trim(val);
          if(val == '')
          {
                elem.val(filter_elemnts[key]);
          }
          else
          {
                elem.val(val);
          }
      }

}
/**
 * 
 */
function submit_filter(data,url,clear)
{
    default_form();
     
    sendData(url, data, function(result)
    {
        $("#main_table_div").replaceWith(result);
        if(clear)
        {
            $("#clear").show();
        }
        else
        {
            resetForm();
            $("#clear").hide();
        }
            
    }, "text");
}
function clear_filter()
{
    var form = $("form#filter-apply");
  
    var href = form.attr("action");
   
    submit_filter({__filter_clear : true},href,false);
   
    return false;
}
function filter_default()
{
    var form = $("form#filter-apply input[type='hidden']").get();
    for(var i = 0; i < form.length; i++)
    {
       var elem = $(form[i]);
       var val = elem.val();
       var key = elem.attr('id').split('-');
       key = key[0];
       filter_elemnts[key] = val;
    }
}
function changeFilter()
{
    var elem = $(this);
    var val  = elem.val();
    if (elem.attr('id') != undefined)
    {
        var key  = elem.attr('id').split('-');
        key = key[0];
        val = trim(val);
        if(val == trim(filter_elemnts[key]))
        {
            elem.val('');
        }
        if(val == '')
        {
            elem.val(trim(filter_elemnts[key]));
        }
    }
}

function clear_filter_form()
{
      var form = $("form#filter-apply input[type='text']").get();
      for(var i = 0; i < form.length; i++)
      {
          var elem = $(form[i]);
          var val = elem.val();
          var key = elem.attr('id').split('-');
          key = key[0];
          val = trim(val);
        
          if(val == filter_elemnts[key])
          {
                  
               elem.val('');
          }
          else
          {
              elem.val(val);
          }
      }
}

