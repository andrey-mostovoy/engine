var swf_file_upload_handler_listeners = {
    swfuploadLoaded: function(event){
        $('.log', this).prepend('<li>Loaded</li>');
    },
    fileQueued: function(event, file){
        $('.log', this).prepend('<li>File queued - '+file.name+'</li>');
        // start the upload since it's queued
        $(this).swfupload('startUpload');
    },
    fileQueueError: function(event, file, errorCode, message){
        $('.log', this).prepend('<li>File queue error - '+message+'</li>');
    },
    fileDialogStart: function(event){
        $('.log', this).prepend('<li>File dialog start</li>');
    },
    fileDialogComplete: function(event, numFilesSelected, numFilesQueued){
        $('.log', this).prepend('<li>File dialog complete</li>');
    },
    uploadStart: function(event, file){
        $('.log', this).prepend('<li>Upload start - '+file.name+'</li>');
    },
    uploadProgress: function(event, file, bytesLoaded, bytesTotal){
        $('.log', this).prepend('<li>Upload progress - '+bytesLoaded+'</li>');
    },
    uploadSuccess: function(event, file, serverData){
        $('.log', this).prepend('<li>Upload success - '+file.name+'</li>');
    },
    uploadComplete: function(event, file){
        $('.log', this).prepend('<li>Upload complete - '+file.name+'</li>');
        // upload has completed, lets try the next one in the queue
        $(this).swfupload('startUpload');
    },
    uploadError: function(event, file, errorCode, message){
        $('.log', this).prepend('<li>Upload error - '+message+'</li>');
    }
};