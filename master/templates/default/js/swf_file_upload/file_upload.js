// set up binding function
$.fn.fileUploadBindAll = function(options) {
	var $this = this;
	$.each(options, function(key, val){
		$this.bind(key, val);
	});
	return this;
}
// set up events handlers
$(function(){
    // variable file_upload_handler_listeners must be defined in handlers
    if(swf_file_upload_handler_listeners == undefined)
    {
        alert('implement swf_file_upload_handler_listeners variable for swfupload');
        return false;
    }
	$('.swfupload-control').fileUploadBindAll(swf_file_upload_handler_listeners);

    //setup each uploader
    $('.swfupload-control').each(function(){
        if(global.swf_file_upload_params[$('.swf_file_upload_type',this).attr('id')] == undefined)
        {
            alert('implement settings for key '+$('.swf_file_upload_type',this).attr('id')+' to global.swf_file_upload_params');
        }
		$(this).swfupload(
            global.swf_file_upload_params[$('.swf_file_upload_type',this).attr('id')]
        );
	});
});