<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Swf File Upload
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

Loader::loadExtension('swfupload.SwfUploadSupplier');

/**
 * class SwfUpload
 * Deal with file upload with swfupload 
 * @package		Extension
 * @subpackage	file upload
 * @author      amostovoy
 */
final class SwfUpload
{
    /**
     * key name of $_FILES array with file
     */
    const FILE_DATA = 'SwfFiledata';
    /**
     * key name of $_SESSION array with tmp files 
     */
    const SESSION_TMP_NAME = 'tmp_files';
    /**
     * key name of $_SESSION array with current hash 
     */
    const SESSION_HASH_NAME = 'tmp_hash';
    /**
     * key name of $_REQUEST array with current hash set by forms 
     */
    const FORM_HASH_NAME = 'swf_fileupload_hash';
    /**
     * This settings just copy of js settings for set in
     * upload init method.
     * version of swfupload is 2.2.0 2009-03-25
     * @var array
     */
    public static $swfUpload = array(
        'button_action' => array(
            'select_file' => -100,
            'select_files' => -110,
            'start_upload' => -120,
        ),
        'cursor' => array(
            'arrow' => -1,
            'hand' => -2,
        ),
        'window_mode' => array(
            'window' => 'window',
            'transparent' => 'transparent',
            'opaque' => 'opaque',
        ),
    );
    /**
     * Flag of right file upload initialize
     * @var bool
     */
    private static $init=false;
    /**
     * current hash name
     * @var string
     */
    private static $hash;
    /**
     * temporary directory to save temp files
     * @var string
     */
    private static $tmp_dir;
    /**
     * Supplier class object
     * @var mixed
     */
    private static $supplier;
    
    /**
     * Initialize fileupload process
     * @param mixed $supplier supplier
     */
    public static function init($supplier=null)
    {
        Loader::loadExtension('Media');
        
        if(!self::restoreSession())
        {
            self::initSession();
        }
        // set tmp directory
        self::$tmp_dir = App::request()->getBaseDir() . Media::GALLERY_DIR . DS . Media::TMP_DIR . DS;
        //set flag
        self::$init = true;
        
        self::supplier($supplier);
    }
    /**
     * init new session hash name 
     */
    public static function initSession()
    {
        self::setHash( self::generateHash() );
    }
    /**
     * try to restore session hash name from REQUEST or SESSION arrays
     * @return boolean true on success
     */
    public static function restoreSession()
    {
        if(isset($_REQUEST[self::FORM_HASH_NAME]))
        {// if we have data from form - restore hash
            self::setHash($_REQUEST[self::FORM_HASH_NAME]);
            return true;
        }
//        elseif(isset($_SESSION[self::SESSION_HASH_NAME]))
//        {// or get hash from session
//            self::setHash( $_SESSION[self::SESSION_HASH_NAME] );
//            return true;
//        }
        return false;
    }
    /**
     * Set supplier for handle few upload situation/
     * {@see FileUploadSupplier}
     * @param mixed $supplier 
     */
    public static function supplier($supplier)
    {
        if(!empty($supplier))
            self::$supplier = $supplier;
    }
    /**
     * Some checks or actions before actual upload.
     * If set supplier call its beforeFileUpload method.
     * {@see FileUploadSupplier}
     * @return boolean 
     */
    private static function beforeUpload()
    {
        // call supplier if exsist
        if(!empty(self::$supplier) 
            && method_exists(self::$supplier, 'swfUploadBeforeUpload')
            && !self::$supplier->swfUploadBeforeUpload()
        ) {
            return false;
        }
        return true;
    }
    /**
     * Upload file to given directory (name only after /gallery/tmp/. Example: image ).
     * @param string $dir directory name where would like place a file
     * @return boolean true on success
     */
    public static function upload($dir)
    {
        // do checks
        if(!self::beforeUpload()
            || !self::checkUpload()
        ) {
            exit();
        }
        // create dir
        File::createDir(self::$tmp_dir . $dir);
        self::$tmp_dir = self::getTmpDirPath($dir);
        // generate tmp file name
        $file_id = self::generateTmpFileName();

        $path_info = pathinfo($_FILES[self::FILE_DATA]['name']);
        $file_extension = $path_info["extension"];
        // save file
        if( self::saveTmpFile($file_id.'.'.$file_extension, $dir) )
        {
            //set to session and return to flash uploder
            self::setToSession($dir, $file_id, $file_extension);
            echo "FILEID:" . $file_id;	// Return the file id to the script. Upload OK
            return true;
        }
        return false;
    }
    /**
     * Do check for upload.
     * If set supplier call its checkFileUpload method.
     * {@see FileUploadSupplier}
     * @return boolean 
     */
    private static function checkUpload()
    {
        $correct=true;
        
        if(!self::$init)
        {
            self::handleError("Need first init file upload");
            $correct=false;
        }
        
        // Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
        $POST_MAX_SIZE = ini_get('post_max_size');
        $unit = strtoupper(substr($POST_MAX_SIZE, -1));
        $multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
        if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE)
        {
            // This will trigger an uploadError event in SWFUpload
            header("HTTP/1.1 500 Internal Server Error");
            self::handleError('POST exceeded maximum allowed size.');
            $correct=false;
        }
        
        // Validate the upload
        if(!isset($_FILES[self::FILE_DATA]))
        {
            self::handleError("No upload found in \$_FILES for " . self::FILE_DATA);
            $correct=false;
        }
        elseif(isset($_FILES[self::FILE_DATA]["error"]) && $_FILES[self::FILE_DATA]["error"] != 0)
        {
            self::handleError(App::lang()->file_upload()->error()->{'err_'.$_FILES[self::FILE_DATA]["error"]});
            $correct=false;
        }
        elseif(!isset($_FILES[self::FILE_DATA]["tmp_name"]) || !@is_uploaded_file($_FILES[self::FILE_DATA]["tmp_name"]))
        {
            self::handleError("Upload failed is_uploaded_file test.");
            $correct=false;
        }
        elseif(!isset($_FILES[self::FILE_DATA]['name']))
        {
            self::handleError("File has no name.");
            $correct=false;
        }
        // call supplier check if exsist
        if(!empty(self::$supplier)
            && method_exists(self::$supplier, 'swfUploadCheck')
            && !self::$supplier->swfUploadCheck()
        ) {
            $correct=false;
        }
        
        return $correct;
    }
    /**
     * generate tmp file name
     * @return string 
     */
    private static function generateTmpFileName()
    {
        return md5(self::getHash() . microtime() . $_FILES[self::FILE_DATA]['tmp_name']);
    }
    /**
     * some actions before actual save uploaded file.
     * If set supplier call its beforeFileUploadSave method.
     * {@see FileUploadSupplier}
     * @param string $file_name temporary file name 
     * @param string $dir dir name, part of path
     * @return boolean 
     */
    private static function beforeSave($file_name, $dir)
    {
        // call supplier if exsist
        if(!empty(self::$supplier)
            && method_exists(self::$supplier, 'swfUploadBeforeSave')
            && !self::$supplier->swfUploadBeforeSave($file_name, $dir)
        ) {
            return false;
        }
        
        return true;
    }
    /**
     * actual save uploaded file
     * @param string $file_name filename
     * @param string $dir dir name, part of path
     * @return boolean 
     */
    private static function saveTmpFile($file_name, $dir)
    {
        File::createDir(self::$tmp_dir);
        
        if(!self::beforeSave($file_name, $dir))
            return false;
        
        if (!@move_uploaded_file($_FILES[self::FILE_DATA]["tmp_name"], self::$tmp_dir.$file_name))
        {
            self::handleError('File could not be saved.');
            return false;
        }
        
        if(!self::afterSave($file_name, $dir))
            return false;
        
        return true;
    }
    /**
     * some actions after save uploaded file
     * @param string $file_name filename
     * @param string $dir dir name, part of path
     * @return boolean 
     */
    private static function afterSave($file_name, $dir)
    {
        // call supplier if exsist
        if(!empty(self::$supplier)
            && method_exists(self::$supplier, 'swfUploadAfterSave')
            && !self::$supplier->swfUploadAfterSave($file_name, $dir)
        ) {
            return false;
        }
        
        return true;
    }
    /**
     * Save to session uploaded temporary file
     * @param string $dir dir name, part of path to save
     * @param string $file_id temp file name
     * @param string $ext file extension
     */
    private static function setToSession($dir, $file_id, $ext)
    {
        $_SESSION[self::SESSION_TMP_NAME][self::$hash][$dir][$file_id] = $file_id.'.'.$ext;
    }
    /**
     * Show error during file upload proccess
     * @param string $error error text
     */
    public static function handleError($error)
    {
        echo 'ERROR:'.$error;
        exit();
    }
    
    /**
     * Delete all temporary files and dir inside tmp dir
     * @return bool return true on success or false on failure
     */
    public static function removeAllTmp()
    {
        return File::clearDir(self::$tmp_dir);
    }
    
    /**
     * Generate random hash for current session
     * @return int
     */
    private static function generateHash()
    {
        return rand(0, 10000);
    }
    /**
     * Set current hash
     * @param int $hash 
     */
    private static function setHash($hash)
    {
        $_SESSION[self::SESSION_HASH_NAME] = self::$hash = $hash;
    }
    /**
     *Get current hash
     * @return int|null
     */
    public static function getHash()
    {
        return isset($_SESSION[self::SESSION_HASH_NAME]) ? $_SESSION[self::SESSION_HASH_NAME] : null;
    }
    
    /**
     * Get array of current tmp files inside given dirrectory
     * @param string $dir dir name, part of path
     * @return array|null
     */
    public static function getTmpArray($dir)
    {
        if(!isset($_SESSION[self::SESSION_TMP_NAME][self::getHash()][$dir]))
            return null;
        
        $base_dir = self::getTmpDirPath($dir);
        $return = array();
        foreach($_SESSION[self::SESSION_TMP_NAME][self::getHash()][$dir] as $file_id => $file)
        {
            if( File::checkExist($base_dir . $file) )
            {
                $return[$file_id] = $file;
            }
        }
        return $return;
    }
    
    /**
     * Get temporary file
     * @param string $dir dir name, part of path
     * @param string $file_id file name
     * @return string|null
     */
    public static function getTmpFile($dir, $file_id)
    {
        return isset($_SESSION[self::SESSION_TMP_NAME][self::getHash()][$dir][$file_id]) ? $_SESSION[self::SESSION_TMP_NAME][self::getHash()][$dir][$file_id] : null;
    }
    /**
     * unset current session hash from session 
     */
    public static function removeHash()
    {
        unset($_SESSION[self::SESSION_HASH_NAME]);
    }
    /**
     * remove temporary file and clean up its row in session
     * @param string $dir dir name, part of path
     * @param string $file_id temp file name
     */
    public static function removeTmpFile($dir, $file_id)
    {
        $file = self::getTmpFile($dir, $file_id);
        $base_dir = self::getTmpDirPath($dir);

        @unlink($base_dir . $file);
        
        unset($_SESSION[self::SESSION_TMP_NAME][self::getHash()][$dir][$file_id]);
    }
    /**
     * Get temporary directory path including hashe dir
     * @param string $dir dir name, part of path
     * @return string
     */
    public static function getTmpDirPath($dir)
    {
        return self::$tmp_dir . $dir . DS . self::getHash() . DS;
    }
    /**
     * Get temporary directory 
     * @return string
     */
    public static function getTmpDir()
    {
        return self::$tmp_dir;
    }
    /**
     * set settings for swfupload in js global var
     * Full list of params see on @link http://demo.swfupload.org/Documentation/
     * or can see here
     * 
     * Here parameters that handled somehow after or befor upload:
     * <code>
     *          'file_size_limit' => $this->data_options['photo_max_size'].' MB',
     *          'file_upload_limit' => $this->data_options['photo_upload_num'],
     *          'post_params'   => array(
     *              'size_limit' => serialize(array(
     *                  'photo' => array(
     *                      'min' => array(
     *                          'w' => $this->data_options["photo_min_x"],
     *                          'h' => $this->data_options["photo_min_y"]
     *                      ),
     *                      'max' => array(
     *                          'w' => $this->data_options["photo_max_x"],
     *                          'h' => $this->data_options["photo_max_y"]
     *                      ),
     *                  )
     *              )),
     *              'duration' => serialize(array(
     *                  'video' => array(
     *                      'min' => $this->data_options["video_min_duration"],
     *                      'max' => $this->data_options["video_max_duration"],
     *                  )
     *              ))
     *          ),
     *          'custom_settings' => array(
     *              'choose_main'           => false,
     *              'additional_methods'    => array(
     *                  'info' => array('frontend.gallery.Photo', 'initManageInfo')
     *              )
     *          )
     * </code>
     * @param string $key
     * @param array $params 
     */
    public static function uploadSettings($key, $params=array())
    {
//        $tpl_path = App::view()->getTemplateElement('file_upload_thumbs_fields');
//        $tpl_path = str_replace('file_upload_thumbs_fields.tpl', '', $tpl_path);
        
        $def = array(
            // Backend settings
            'upload_url' => App::controller()->domain_url . '/swfupload/upload',
            'file_post_name' => self::FILE_DATA,
            'post_params' => array(
                Session::SESSION_NAME => session_id(),
                self::FORM_HASH_NAME => self::getHash(),
            ),
            // Flash Settings
            'flash_url' => App::view()->getCommonJsUrl() . '/swf_file_upload/vendor/swfupload/swfupload.swf',
            // Flash file settings
            'file_size_limit' => "1000 MB",
            'file_types' => "*.*",
            'file_types_description'=> "",
            'file_upload_limit' => 0,
            'file_queue_limit' => 0,
            //Other flash settings
            'use_query_string' => false,
            'requeue_on_error' => false,
            'http_success' => array(201, 202),
            'assume_success_timeout' => 0,
            'prevent_swf_caching' => false,
            'preserve_relative_urls' => false,
            // Debug settings
            'debug' => false,
            // Button Settings
            'button_placeholder_id' => "spanButtonPlaceholder",
            'button_image_url' => App::view()->getCommonImageUrl() . '/swf_file_upload/XPButtonUploadText_61x22.png',
            'button_width' => 86,
            'button_height' => 22,
//            'button_text' => '<b>Click</b> <span class="redText">here</span>',
            'button_text_style' => ".redText { color => #FF0000; }",
            'button_text_left_padding' => 0,
            'button_text_top_padding' => 0,
            'button_disabled' => false,
            'button_action' => self::$swfUpload['button_action']['select_files'],
            'button_cursor' => self::$swfUpload['cursor']['hand'],
            'button_window_mode' => self::$swfUpload['window_mode']['window'],
            // --------- Event handlers settings - all set in js  ------------
//            swfupload_loaded_handler => swfupload_loaded_function,
//            file_dialog_start_handler => file_dialog_start_function,
//            file_queued_handler => file_queued_function,
//            upload_start_handler => upload_start_function,
//            debug_handler => debug_function,            
//            'file_queue_error_handler' => "fileQueueError",
//            'file_dialog_complete_handler' => "fileDialogComplete",
//            'upload_progress_handler' => "uploadProgress",
//            'upload_error_handler' => "uploadError",
//            'upload_success_handler' => "uploadSuccess",
//            'upload_complete_handler' => "uploadComplete",
            //custom
            'custom_settings' => array(
                'progress_target' => 'fsUploadProgress',
                'upload_successful' => false,
                self::FORM_HASH_NAME => self::getHash(),
                'delete_tmp'    => App::controller()->domain_url . "/swfupload/deleteTmp",
//                'images'        => App::view()->getCommonImageUrl().'/swf_file_upload/',
//                'tpl_path'      => urlencode(addslashes($tpl_path))
            )
        );

        $params = self::mergeParamsArrays($def, $params);

//        App::view()->addJsLangVar(array('media'=>$this->_lang->media()->all()));
        
        $js_vars = App::view()->getJsVar('swf_file_upload_params');
        if(is_null($js_vars))
        {
            $js_vars = array();
        }
        $js_vars[$key] = $params;
        App::view()->addJsVar(array(
            'swf_file_upload_params' => $js_vars,
        ));
    }
    /**
     * Merge multidimensional arrays for swfUploadSettings
     * 
     * @param array $array1
     * @param array $array2
     * @return merged array
     */ 
    public static function mergeParamsArrays($array1, $array2)
    {
        foreach($array1 as $key => &$arr)
        {
            if (isset($array2[$key]))
            {
                if (is_array($arr) || is_array($array2[$key]))
                {
                    $arr = array_merge((array)$arr, (array)$array2[$key]);
                }
                else
                {
                    $arr = $array2[$key];
                }
            }
        }
        $array1 += array_diff($array2, $array1);
        $array1 += array_diff_assoc($array2, $array1);

        return $array1;
    }
    /**
     * move uploaded SINGLE tmp file to actual diretory.
     * Use Curl or redirection method for it. For all methods 
     * call {@see SwfUploadController::moveTmpFilesAction}
     * @param string $from_dir from dir name, part of path
     * @param string $to_dir to dir path
     * @param string $id id of entity what file is consider
     * @param string $method (optional d:'curl') set method of moving this files. 
     * Allow curl or redirect
     * @param string|null $next (optional d:null) if method is redirect need set next url
     * to redirect
     * @todo create functionality to move multiple files
     */
    public static function moveTmpFiles($from_dir, $to_dir, $id, $method='curl', $next=null)
    {
        if($method == 'curl')
        {
            Loader::loadExtension('Curl');
            $curl = new Curl();
            $curl->setOption(CURLOPT_FTP_CREATE_MISSING_DIRS, true);
            $curl->addPostFields(array(
                '_curl' => true,
                'from_dir' => $from_dir,
                'to_dir' => $to_dir,
                'id' => $id,
                self::FORM_HASH_NAME => self::getHash(),
                Session::SESSION_NAME => session_id(),
            ));
            session_write_close();
            $res = json_decode($curl->post(App::request()->getBaseUrl().'/swfupload/movetmpfiles'));
            if(!empty($res))
            {
                foreach($res as &$f)
                {
                    self::removeTmpFile($from_dir, $f);
                }
            }
        }
        elseif($method == 'redirect')
        {
            $_SESSION['from_dir'] = $from_dir;
            $_SESSION['to_dir'] = $to_dir;
            $_SESSION['id'] = $id;
            $_SESSION['next_url'] = $next;
            App::controller()->redirect('/swfupload/movetmpfiles?'.self::FORM_HASH_NAME.'='.self::getHash());
        }
    }
}
/* End of file SwfUpload.php */
/* Location: ./class/extension/SwfUpload.php */
?>