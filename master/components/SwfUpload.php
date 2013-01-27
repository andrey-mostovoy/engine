<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Component
 * @author amostovoy
 * @filesource
 */

Loader::loadExtension('swfupload.SwfUpload');

/**
 * class SwfUploadComponent.
 * Handle common file upload actions from swfupload.
 * Such a upload itself, delete temporary files, show temporary file thumbnail 
 * (for image). After component finish work they unset SwfUpload session hash 
 * name, but if no error before it finished.
 * To restore it need to send it via REQUEST in SwfUpload::FORM_HASH_NAME key
 *
 * @package		Project
 * @subpackage	Component
 * @author		amostovoy
 */
class SwfUploadComponent extends CommonController implements SwfUploadSupplier
{
    protected function _init()
    {   
        SwfUpload::init($this);
        Loader::loadExtension('Media');
    }
    
    protected function createControllerModel($model, $site_part = true)
    {
    }
    
    protected function setDefaultBreadCrumb()
    {
    }
    
    protected function setDefaultSiteTitle()
    {
    }
    /**
     * additional actions or checks before upload
     * {@see FileUploadSupplier}
     * @return boolean true if allow upload, false otherwise
     */
    public function swfUploadBeforeUpload()
    {
        return true;
    }
    /**
     * upload itself 
     */
    public function uploadAction()
    {
        SwfUpload::upload('image');
    }
    /**
     * additional check of uploaded file
     * {@see FileUploadSupplier}
     * @return boolean true if all good, false otherwise
     */
    public function swfUploadCheck(){return true;}
    /**
     * Additional checks or actions before save of uploaded file
     * {@see FileUploadSupplier}
     * @param string $file_name 
     * @param string $dir 
     * @return boolean true if allow save uploaded file, false otherwise
     */
    public function swfUploadBeforeSave($file_name, $dir)
    {
        // check video for duration. Needed ffmpeg extension for php
        // {@see SwfUpload::uploadSettings} for more detail
        if(defined('Media::VIDEO_DIR') && $dir == Media::VIDEO_DIR)
        {
            $movie = new ffmpeg_movie($_FILES[SwfUpload::FILE_DATA]["tmp_name"]);
            
            if(($duration = $this->_request->getPost('duration', false)))
            {
                $duration = unserialize($duration);
       
                if(isset($duration['video']['min']))
                {
                    if($movie->getDuration() < $duration['video']['min'])
                    {
                        SwfUpload::handleError('Video duration to short');
                        return false;
                    }
                }
                if(isset($duration['video']['max']))
                {
                    if($movie->getDuration() > $duration['video']['max'])
                    {
                        SwfUpload::handleError('Video duration to long');
                        return false;
                    }
                }
            }
            elseif(Defines::MAX_VIDEO_DURATION > 0)
            {
                if($movie->getDuration() > Defines::MAX_VIDEO_DURATION)
                {
                    SwfUpload::handleError('Video duration to long');
                    return false;
                }
            }
        }
        // check for image dimensions. {@see SwfUpload::uploadSettings} for more detail
        if(defined('Media::IMAGE_DIR') && $dir == Media::IMAGE_DIR)
        {
            if(($size_limit = $this->_request->getPost('size_limit', false)))
            {
                $size_limit = unserialize($size_limit);
                
                $size = getimagesize($_FILES[SwfUpload::FILE_DATA]["tmp_name"]);
                $w = $size[0];
                $h = $size[1];
                if(isset($size_limit['photo']['min']))
                {
                    if($w < $size_limit['photo']['min']['w'] || $h < $size_limit['photo']['min']['h'])
                    {
                        SwfUpload::handleError('File size(w x h) to low');
                        return false;
                    }
                }
                if(isset($size_limit['photo']['max']))
                {
                    if($w > $size_limit['photo']['max']['w'] || $h > $size_limit['photo']['max']['h'])
                    {
                        SwfUpload::handleError('File size(w x h) to high');
                        return false;
                    }
                }
            }
        }
        return true;
    }
    /**
     * Additional action after save uploaded file
     * {@see FileUploadSupplier}
     * @param string $file_name
     * @param string $dir
     * @return boolean true if all ok, false otherwise
     */
    public function swfUploadAfterSave($file_name, $dir)
    {
        return true;
    }
    /**
     * load template for uploaded temp file item thumb
     */
    public function loadTmpThumbItemAction()
    {
        $item = array();
        $file_id = $this->_request->getPost('id', null, Request::FILTER_STRING);
        $url_show = '';
        $url_del = '';
        if($this->_request->getPost('error', false, Request::FILTER_BOOL))
        {
            $url_show = $this->_view->getCommonImageUrl().'/swf_file_upload/'.$file_id;
            $url_del = '#';
        }
        else
        {
            $url_show = $this->domain_url . '/swfupload/showtmpthumbnail'
                .'/id/'.$file_id
                .'?'.SwfUpload::FORM_HASH_NAME.'='.SwfUpload::getHash();
            $url_del = $this->domain_url . '/swfupload/deletetmpfile'
                .'/id/'.$file_id
                .'?'.SwfUpload::FORM_HASH_NAME.'='.SwfUpload::getHash();
        }
//        if(is_null($dir)) $dir = Media::IMAGE_DIR;
        $this->_view->item = array(
            'url_show' => $url_show,
            'url_del' => $url_del,
        );
        $tpl = $this->fetchElementTemplate('swf_file_upload_thumb_item');
        $this->ajax->send(Ajax::RESULT_HTML, $tpl);
    }
    /**
     * show thumbnail of uploaded tmp image.
     * Used inside src of image tag
     * @param string $dir 
     */
    public function showTmpThumbnailAction($dir = null)
    {
        if(is_null($dir)) $dir = Media::IMAGE_DIR;
        $file_id = $this->_request->getParam('id', null, Request::FILTER_STRING);
        
        $img_file = SwfUpload::getTmpFile($dir, $file_id);
        if(!empty($file_id) && !empty($img_file))
        {
            File::outputFile( SwfUpload::getTmpDirPath($dir) . Media::THUMB . '_' . $img_file );
        }
        else
        {
            SwfUpload::handleError('no file id');
        }
    }
    /**
     * for cron purpose. Clean up all tmp folder
     */
    public function removeAllTmpAction()
    {
        SwfUpload::removeAllTmp();
        exit();
    }
    /**
     * remove temporary file
     * @param string $dir 
     */
    public function deleteTmpFileAction($dir=null,$id=0)
    {
        if(is_null($dir)) $dir = Media::IMAGE_DIR;
        $file_id = $this->_request->getParam('id', $id, Request::FILTER_STRING);
        SwfUpload::removeTmpFile($dir, $file_id);
        if($this->_request->isAjax())
        {
            $this->ajax->send(Ajax::RESULT_OK);
        }
    }
    /**
     * Move uploaded file to needed directory.
     * Also delete from session its identifier 
     */
    public function moveTmpFilesAction()
    {
        $res = array();
        
        $from = $this->_request->getPost('from_dir', isset($_SESSION['from_dir'])?$_SESSION['from_dir']:'', Request::FILTER_STRING);
        $to = $this->_request->getPost('to_dir', isset($_SESSION['to_dir'])?$_SESSION['to_dir']:'', Request::FILTER_STRING);
        $id = $this->_request->getPost('id', isset($_SESSION['id'])?$_SESSION['id']:'', Request::FILTER_INT);
        $is_curl = $this->_request->getPost('_curl', false, Request::FILTER_BOOL);

        File::createDir($to.$id);
        foreach(SwfUpload::getTmpArray($from) as $k => $f)
        {
            File::moveFile(
                SwfUpload::getTmpDirPath($from).$f,
                $to.$id.DS.Media::ORIGIN.'.'.Media::$ext
            );
            $this->deleteTmpFileAction($from,$k);
            $res[] = $k;
        }
        if($is_curl)
            die(json_encode($res));
        else
        {
            $this->redirect($_SESSION['next_url']);
        }
    }
    /**
     * overide parent action for clean up session swfupload hash name 
     */
    public function _end()
    {
        SwfUpload::removeHash();
    }
}

/* End of file SwfUpload.php */
/* Location: ./components/SwfUpload.php */
?>