var swf_file_upload_handler_listeners = {
    swfuploadLoaded: function(event){
    },
    fileQueued: function(event, file){
        $('#txtFileName', this).val(file.name);
        // start the upload since it's queued
        $(this).swfupload('startUpload');
    },
    fileQueueError: function(event, file, errorCode, message){
        // Handle this error separately because we don't want to create a FileProgress element for it.
        var inst = $.swfupload.getInstance(this);
		switch (errorCode) {
            case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
                alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
                return;
            case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                alert("The file you selected is too big.");
                inst.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                return;
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                alert("The file you selected is empty.  Please select another file.");
                inst.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                return;
            case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
                alert("The file you choose is not an allowed file type.");
                inst.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                return;
            default:
                alert("An error occurred in the upload. Try again later.");
                inst.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                return;
		}
    },
    fileDialogStart: function(event){
        $("#txtFileName", this).val('');

        $(this).swfupload('cancelUpload');
    },
    fileDialogComplete: function(event, numFilesSelected, numFilesQueued){
    },
    uploadStart: function(event, file){
    },
    uploadProgress: function(event, file, bytesLoaded, bytesTotal){
        var inst = $.swfupload.getInstance(this);
        var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		file.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
		var progress = new FileProgress(file, inst.customSettings.progress_target);
		progress.setProgress(percent);
		progress.setStatus("Uploading...");
    },
    uploadSuccess: function(event, file, serverData){
        var inst = $.swfupload.getInstance(this);
        file.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
		var progress = new FileProgress(file, inst.customSettings.progress_target);
		progress.setComplete();
		progress.setStatus("Complete.");
		progress.toggleCancel(false);
        
        if(typeof console !== undefined && global.debug)
        {
            console.log(serverData);
        }
        
		if (serverData === " " || serverData == '' || serverData.substring(0, 6) == 'ERROR:') {
			inst.customSettings.upload_successful = false;
			inst.customSettings.error_text = serverData.substring(6);
		} else {
			inst.customSettings.upload_successful = true;
			$("#hidFileID").val(serverData.substring(7));
		}
    },
    uploadComplete: function(event, file){
        var inst = $.swfupload.getInstance(this);
        if (inst.customSettings.upload_successful) {
			inst.setButtonDisabled(true);
		} else {
			file.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
			var progress = new FileProgress(file, inst.customSettings.progress_target);
			progress.setError();
			progress.setStatus("File rejected. There was a problem with the upload: <strong>"+inst.customSettings.error_text+"</strong>");
			progress.toggleCancel(false);
			
			$("#txtFileName").val('');
		}
        // upload has completed, lets try the next one in the queue
        $(this).swfupload('startUpload');
    },
    uploadError: function(event, file, errorCode, message){
        if (errorCode === SWFUpload.UPLOAD_ERROR.FILE_CANCELLED) {
			// Don't show cancelled error boxes
			return;
		}
		
		$("#txtFileName").val('');
		var inst = $.swfupload.getInstance(this);
		// Handle this error separately because we don't want to create a FileProgress element for it.
		switch (errorCode) {
            case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
                alert("There was a configuration error.  You will not be able to upload a resume at this time.");
                inst.debug("Error Code: No backend file, File name: " + file.name + ", Message: " + message);
                return;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
                alert("You may only upload 1 file.");
                inst.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                return;
            case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
            case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
                break;
            default:
                alert("An error occurred in the upload. Try again later.");
                inst.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                return;
		}

		file.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
		var progress = new FileProgress(file, inst.customSettings.progress_target);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
            case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
                progress.setStatus("Upload Error");
                inst.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
                progress.setStatus("Upload Failed.");
                inst.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.IO_ERROR:
                progress.setStatus("Server (IO) Error");
                inst.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
                progress.setStatus("Security Error");
                inst.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
                progress.setStatus("Upload Cancelled");
                inst.debug("Error Code: Upload Cancelled, File name: " + file.name + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
                progress.setStatus("Upload Stopped");
                inst.debug("Error Code: Upload Stopped, File name: " + file.name + ", Message: " + message);
                break;
		}
    }
};