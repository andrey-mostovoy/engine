<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

Loader::LoadClass('Common_Media');

/***************************************************
 *  HERE CAN CHANGE SOME BEHAVIOR FOR PROJECT      *
 ***************************************************/

/**
 * class Media
 * containing methods to deel with images - resize, crop, rotate etc..
 * for more details {@see MediaCommon}
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 */
class Media extends MediaCommon
{
    const MIDDLE                    = 'middle';
    // set default extension for files
//    protected static $ext = 'png';
        
    protected function projectSizes()
    {
        return array(
            self::MIDDLE    => array('w' => 200, 'h' => 200),
        );
    }
    
    public function createProfileThumbs()
    {
        $this->createThumbnails(self::PROFILE_DIR);
    }
    /**
     * set up media url's and dir's for project
     */
    public static function mediaUrls()
    {
        // call parent to initialize common urls
        parent::mediaUrls();
        
       	App::controller()->profile_img_dir = App::controller()->img_dir . self::PROFILE_DIR . DS;
        App::controller()->profile_url = App::controller()->img_url . self::PROFILE_DIR. '/';
        

        App::view()->addDir('profile', self::PROFILE_DIR);
        App::view()->addImg('profile', App::controller()->profile_url);
    }
}

// set up media urls for project
Media::mediaUrls();

/* End of file Media.php */
/* Location: ./class/extension/Media.php */
?>