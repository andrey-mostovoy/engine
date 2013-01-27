<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadComponent('SwfUpload');

/**
 * class SwfUploadController
 *
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class SwfUploadController extends SwfUploadComponent
{
    public function swfUploadBeforeUpload()
    {
        if(!$this->_user->isLogin())
        {
            SwfUpload::handleError('require login');
            return false;
        }
        return true;
    }
    
    /**
     * upload itself 
     */
    public function uploadCvAction()
    {
        SwfUpload::upload('cv');
    }
    
    public function swfUploadAfterSave($file_name, $dir)
    {
        if(defined('Media::IMAGE_DIR') && $dir == Media::IMAGE_DIR)
        {
            $media = new Media(SwfUpload::getTmpDir().$file_name, SwfUpload::getTmpDir());
            if( !$media->createTmpThumb(Media::THUMB.'_'.$file_name) )
            {
                SwfUpload::handleError('Thumb could not be created.');
                return false;
            }
        }
        return true;
    }
    
    public function deleteTmpFileAction($dir=null,$id=0)
    {
        if(is_null($dir)) $dir = Media::IMAGE_DIR;
        if($dir == Media::IMAGE_DIR)
        {
            $file_id = $this->_request->getParam('id', $id, Request::FILTER_STRING);
            @unlink(SwfUpload::getTmpDirPath($dir) 
                    . 'thumb_'. SwfUpload::getTmpFile($dir, $file_id));
        }
        parent::deleteTmpFileAction($dir);
    }
}

/* End of file SwfUpload.php */
/* Location: ./controllers/SwfUpload.php */
?>