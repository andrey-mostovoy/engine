<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Common
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.11
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass('Base.Controller.ControllerValidation');

/**
 * class CommonController
 * containing methods and class properties for current project only
 *
 * @package		Base
 * @subpackage	Common
 * @category	Controllers
 * @author		amostovoy
 * @abstract
 */
abstract class CommonController extends ControllerValidation
{
	/**
	 * initialize some variables for current project.
	 * DO NOT REMOVE THIS METHOD
	 */
	protected function _commonInit()
	{
	}

    /*********************************************
	 *    PLACE YOUR CODE BELOW. Have Fun! =)    *
	 *********************************************/
    
    /**
     * Check file
     *
     * @param (array) $file
     * @param (bool) $isRequired if no file - error will triggered
     * @return (bool)
     */
    protected function _checkFile($file, $isRequired = true, $addErrors = true, $maxSize = 0)
    {
        if (!$file) {
            if ($isRequired && $addErrors)
            {
                $this->addErrors('empty_file');
            }
            return false;
        }
        elseif (UPLOAD_ERR_NO_FILE == $file)
        {
            if ($isRequired && $addErrors)
            {
                $this->addErrors('empty_file');
            }
            return false;
        }
        elseif ($maxSize && !empty($file['size']) && $maxSize < $file['size'])
        {
            if ($addErrors)
            {
                $this->addErrors('large_file');
            }
            return false;
        }
        elseif (!is_array($file))
        {
            if ($addErrors)
            {
                $this->addErrors('wrong_file');
            }
            return false;
        }

        return true;
    }
    
    
    
    
    
    protected final function imageUploadSettings($params=array())
    {
        $def = array(
            'upload_url' => $this->domain_url . "/file/uploadImage",

            'file_types' => "*.jpg;*.gif;*.png",
            'file_types_description'=> "Web Image Files",
            'file_size_limit' => "20 MB",
            'file_upload_limit' => 0,
            'file_queue_limit' => 0,

            'button_placeholder_id' => "spanImageButtonPlaceholder",

            'button_width' => 86,
            'button_height' => 22,
            'button_text' => '<span class="redText">Add Photo(s)</span>',
            'button_text_style' => ".redText { color => #FF0000; }",
            'button_text_left_padding' => 5,
            'button_text_top_padding' => 2,

            'custom_settings' => array(
                'upload_target' => "divImageProgressContainer",
                'thumb_target' => "thumbnailsImage",
                'show_url' => "showImageThumbnail",
                'ftype' => "img",
                'delete_tmp' => $this->domain_url . "/file/deleteTmpImage"
            )
        );

        if(!empty($params))
        {
            $params = $this->mergeParamsArrays($def, $params);
        }

        $this->swfUploadSettings('image', $params);
    }
    
    protected final function videoUploadSettings($params=array())
    {
        $def = array(
            'upload_url' => $this->domain_url . "/file/uploadVideo",

            'file_types' => "*.3g2;*.3gp;*.3gpp;*.asf;*.avi;*.flv;*.f4v;*.mpeg;*.mpe;*.mpg;*.dat;*.m4v;*.mp4;*.mpeg4;*.mkv;*.mov;*.qt;*.nsv;*.ogm;*.ogv;*.vob;*.wmv;*.divx",
            'file_types_description'=> "Video Files",
            'file_size_limit' => "1024 MB",
            'file_upload_limit' => 0,
            'file_queue_limit' => 0,

            'button_placeholder_id' => "spanVideoButtonPlaceholder",

            'button_width' => 86,
            'button_height' => 22,
            'button_text' => '<span class="redText">Add Video(s)</span>',
            'button_text_style' => ".redText { color => #FF0000; }",
            'button_text_left_padding' => 5,
            'button_text_top_padding' => 2,

            'custom_settings' => array(
                'upload_target' => "divVideoProgressContainer",
                'thumb_target' => "thumbnailsVideo",
                'show_url' => "showVideoThumbnail",
                'ftype' => "video",
                'delete_tmp'    => $this->domain_url . "/file/deleteTmpVideo"
            )
        );
        
        if(!empty($params))
        {
            $params = $this->mergeParamsArrays($def, $params);
        }
        
        $this->swfUploadSettings('video', $params);
    }
    
    protected final function documentUploadSettings($params=array())
    {
    }
    
    protected function setSelectedMainMenu($selected)
    {
        if(!empty($selected))
        {
            $this->_view->selected_main_menu = $selected;
        }
        return true;
    }
    protected function setSelectedSectionMenu($selected)
    {
        if(!empty($selected))
        {
            $this->_view->selected_section_menu = $selected;
        }
        return true;
    }
    
    protected function imageSave($media_method, $save_dir, $parent_id, $source='', $main_img_db_key='photo')
    {
        $main_photo_meta_id = $this->tmpImageSave($media_method, $save_dir, $parent_id, $source);
        
        if (isset($this->data['img'])) {
            foreach($this->data['img'] as $k=>$v)
            {
                $this->model->saveImage($parent_id, $v, $source, $k);
            }
        }

        if(!empty($this->main_photo) && $main_photo_meta_id)
        {
            $this->data[$main_img_db_key] = $main_photo_meta_id;
            $this->model->save($this->data, array('meta_upd'=>false), $parent_id);
        }
    }
    
    protected function tmpImageSave($media_method, $save_dir, $parent_id, $source='')
    {
        $this->main_photo = (empty($this->data['main_img']) ? '' : $this->data['main_img']);
            
        return $this->saveTmpImages($media_method, $save_dir, $parent_id, $source, $this->main_photo);
    }
    
    protected function videoSave($save_dir, $parent_id, $source = '')
    {
        $this->saveTmpVideos($save_dir, $parent_id, $source);
        
        if(isset($this->data['video_url']))
        {
            $r = array();
            foreach($this->data['video_url'] as $k=>$video)
            {
                if(!empty($video['url']))
                {
                    $r[] = Media::parseVideoResourceUrl($video['url']) + array('is_url'=>true);
                }
            }
            if(!empty($r))
            {
                $this->model->saveVideo($r, $parent_id, $this->data['video_url']);
            }
        }
        
        if(isset($this->data['video']))
        {
            $this->model->saveVideo($this->data['video'], $parent_id);
        }
    }
    
    /**
     * initialize file upload process
     */
    protected final function initFileUpload()
    {
        Loader::loadController('FileUploadController', false);
        
        FileUploadController::initSession();
        
        $this->_view->addJsLangVar(array('error' => $this->_lang->form()->error()->all()));
    }
    
    /**
     * save images from tmp dir to item dir on save action
     * @param string $media_method - media method name to call to resize images
     * @param string $save_dir - dir name to save images
     * @param int $item_id - id of item
     * @param string $source - source type
     * @param string $main_photo_file_id - main photo session identification
     */
    protected final function saveTmpImages($media_method, $save_dir, $item_id, $source='', $main_photo_file_id='')
    {
        if(!empty($media_method) && !empty($save_dir))
        {
             
            Loader::loadController('FileUploadController', false);
//echo '<pre>';
//var_dump($this->_user->current_profile['id']);
//var_dump($this->_user->getId());
//echo '</pre>';
//die();
            $files = FileUploadController::getTmpImages($this->_user->getId());
            
            if(is_array($files) && !empty($files))
            {
                $tmp_dir = FileUploadController::getTmpImageDir();
                
//                ini_set('max_execution_time', -1);
//                ini_set('memory_limit', -1);
                
                foreach($files as $file_id => $file)
                {
                    $id = $this->model->saveImage($item_id, $this->data['img'][$file_id], $source);
                   
                    if($file_id == $main_photo_file_id)
                    {
                        $main_photo_file_id = $id;
                    }
                    if(!isset($media) || !($media instanceof Media))
                    {
                        $media = new Media($tmp_dir . $file, '');
                    }
                    else
                    {
                        $media->next($tmp_dir . $file);
                    }
                    File::createDir($save_dir . $id);
                    $media->setDir($save_dir . $id . DS);
                    
                    $media->$media_method();
                    FileUploadController::removeTmpImageFile($file_id, $this->_user->getId());
                }
                if(empty($main_photo_file_id))
                {
                    return true;
                }
                else
                {
                    return $main_photo_file_id;
                }
            }
            else
            {
                if(!empty($main_photo_file_id))
                {
                    return $main_photo_file_id;
                }
            }
        }
        return false;
    }
    
    /**
     * save videos from tmp dir to item dir on save action
     * @param string $media_method - media method name to call to resize images
     * @param string $save_dir - dir name to save images
     * @param int $item_id - id of item
     * @param string $source - source type
     * @param string $main_photo_file_id - main photo session identification
     */
    protected final function saveTmpVideos($save_dir, $item_id, $source='', $main_photo_file_id='')
    {
        
        if(!empty($save_dir))
        {
            Loader::loadController('FileUploadController', false);
            
            $files = FileUploadController::getTmpVideos($this->_user->getId());
            
            if(!empty($files) && is_array($files))
            {
                $tmp_dir = FileUploadController::getTmpVideoDir();
                $ids = $this->model->saveVideo($files, $item_id, $this->data['video'], $source);
                if(!empty($ids))
                {
                    File::createDir($save_dir);
                    foreach($ids as $k=>$id)
                    {
                        File::createDir($save_dir . $id);
                        File::moveFile($tmp_dir.$files[$k], $save_dir.$id.DS.$files[$k]);
                        FileUploadController::removeTmpVideoFile($k, $this->_user->getId());
                        unset($this->data['video'][$k]);
                    }
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * render form for uploaded media
     */
    public function showUploadedMediaAction()
    {
        App::controller('ShowContentCommon')->tmpThumbAction();
    }
    
    
    
    //    NEW content
    
    /**
     * Create options for select dropdawn
     * @param bool $sex
     * @param bool $marital
     * @param bool $country
     * @param bool $language_knowledge
     * @return array 
     */
    protected function createSelectOptions($sex=true,$marital=true,$country=true,$language_knowledge=true)
    {
        $info = array();
        if($sex)
        {
            $info['sex'] = array(
                array(
                    'id' => 'male',
                    'name' => $this->_lang->user()->sex()->male
                ),
                array(
                    'id' => 'female',
                    'name' => $this->_lang->user()->sex()->female
                ),
            );
        }
        if($marital)
        {
            $info['marital'] = $this->model('Marital', false)->get();
        }
        if($country)
        {
            $info['country'] = $this->model('Country', false)->get();
        }
        if($language_knowledge)
        {
            $info['language_knowledge'] = array(
                array(
                    'id' => 'beginner',
                    'name' => $this->_lang->language_knowledge()->beginner,
                ),
                array(
                    'id' => 'elementary',
                    'name' => $this->_lang->language_knowledge()->elementary,
                ),
                array(
                    'id' => 'intermediate',
                    'name' => $this->_lang->language_knowledge()->intermediate,
                ),
                array(
                    'id' => 'upper_intermediate',
                    'name' => $this->_lang->language_knowledge()->upper_intermediate,
                ),
                array(
                    'id' => 'advanced',
                    'name' => $this->_lang->language_knowledge()->advanced,
                ),
                array(
                    'id' => 'proficient',
                    'name' => $this->_lang->language_knowledge()->proficient,
                ),
            );
        }
        return $info;
    }
}
/* End of file Controller.php */
/* Location: ./class/Common/Controller.php */
?>
