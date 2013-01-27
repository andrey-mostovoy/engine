<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

/***************************************************
 *                 DO NOT EDIT                     *
 ***************************************************/

Loader::LoadLib('media/Image');

/**
 * class MediaCommon
 * <p>
 * containing methods to deel with images - resize, crop, rotate etc..
 * major logic is extend Image lib that can use as GD2 so MagicMagic if last
 * is available.
 * </p>
 * <p>
 * Class create files inside /gallery directory. Main structure logic is:
 * /gallery/%type%/%entity%/%id%/%files%
 * Here:
 * <ul>
 *  <li>
 *      %type% - dir, media type, i.e.  image
 *  </li>
 *  <li>
 *      %entity% - dir, some project depending folder, i.e.  profile
 *  </li>
 *  <li>
 *      %id% - dir, entity identification, i.e.  5
 *  </li>
 *  <li>
 *      %files% - files of original uploaded file and its copies, croped or something else
 *  </li>
 * </ul>
 * So we have something like that:
 * {@example /gallery/image/profile/5/original.jpg}
 * and so on..
 * </p>
 * <p>
 * Also in Smarty we have image template function that accept few params,
 * such as dir name, entity id so on... and try to find image inside given
 * folder, if can't - try to use default image for entity or for all project
 * </p>
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 */
class MediaCommon extends Image
{
    /**
     * file name for original size
     */
    const ORIGIN                    = 'origin';
    /**
     * file name for thumbnail size
     */
    const THUMB                     = 'thumb';
    /**
     * file name for icon size
     */
    const ICON                      = 'icon';
    /**
     * file name for profile image size
     */
    const PROFILE                   = 'profile';
    
    /**
     * Main dir name for storing files
     */
    const GALLERY_DIR               = 'gallery';
    /**
     * Default dir for storing default images for
     * entity or all project 
     */
    const DEFAULT_DIR               = 'default';
    /**
     * Dir name for save in images
     */
    const IMAGE_DIR                 = 'image';
    /**
     * Dir name for save temporary files, for example from
     * swfupload 
     */
    const TMP_DIR                   = 'tmp';
    /**
     * dir name to save in user profile images
     */
    const PROFILE_DIR               = 'profile';
	/**
     * Default extension for saved files
     */
	public static $ext = 'jpg';
    /**
     * widths and heights of image files in project
     * @var array
     */
    private $sizes = array(
        self::THUMB     => array('w' => 100, 'h' => 100),
        self::ICON      => array('w' => 50, 'h' => 50),
        self::PROFILE   => array('w' => 170, 'h' => 170),
    );
	/**
     * Array with files for sections in project.
     * Also can set here a individual size of file.
     * @example
     * <code>
     * self::PROFILE_DIR => array(
     *     self::THUMB => array('w' => 10,'h' => 10),
     * ),
     * </code>
     * @var array
     */
	private $section_sizes = array(
        self::PROFILE_DIR => array(
            self::THUMB,
        ),
        self::TMP_DIR   => array(
            self::THUMB
        ),
	);
    /**
     * directory path to save in files
     * @var string
     */
	protected $dir = null;

	/**
     * Class construct
     * @param string $image path to image
     * @param string $img_dir path to dir to save in file
     * @param mixed $config (optional d:null) configuration for Image lib.
     * {@see Image}
     */
	public function __construct($image=null, $img_dir='', $config = null)
	{
        if(!empty($img_dir))
            $this->setDir($img_dir);

        if(!empty($image))
            parent::__construct($image, $config);
        
        $this->addSizes($this->projectSizes());
        $this->addSectionSizes($this->projectSectionSizes());
	}
    /**
     * Get sizes for project
     * @return array|null
     */
    protected function projectSizes()
    {
        return null;
    }
    /**
     * Get section file belonging and its sizes for project
     * @return array|null
     */
    protected function projectSectionSizes()
    {
        return null;
    }
    /**
     * Set directory to save in 
     * @param string $dir dir path
     */
    public function setDir($dir)
    {
        if(!empty($dir))
        {
            $this->dir = $dir;
        }
    }
    /**
     * add sizes of images in project
     * @example
     * <code>
     * array(
     *   self::THUMB     => array('w' => 100, 'h' => 100),
     *   self::ICON      => array('w' => 50, 'h' => 50),
     *   self::PROFILE   => array('w' => 170, 'h' => 170),
     * );
     * </code>
     * @param array $sizes 
     */
    protected final function addSizes($sizes)
    {
        if(!empty($sizes) && is_array($sizes))
        {
            $this->sizes += $sizes;
        }
    }
    /**
     * add section size images belonging in project
     * @example
     * <code>
     * * array(
     *   self::PROFILE_DIR => array(
     *      self::THUMB,
     *      self::ICON => size('w'=>33,'h'=>35), // here set individual sizes
     * ),
     * </code>
     * @param array $section_sizes 
     */
    protected final function addSectionSizes($section_sizes)
    {
        if(!empty($section_sizes) && is_array($section_sizes))
        {
            $this->section_sizes += $section_sizes;
        }
    }
    /**
     * Retrieve size values for given section
     * @param string $section section name
     * @param array|null|string $size section file individual sizes or file name
     * for that sizes belong
     * @return null|array if found sizes in system return array, null otherwise
     */
    public function getSize($section, $size=null)
    {
        if(!empty($section) && !empty($size))
        {
            if (is_array($size))
            {
                return $size;
            }
            elseif(isset($this->sizes[$size]))
            {
                if(isset($this->section_sizes[$section][$size]))
                    return $this->section_sizes[$section][$size];
                elseif(isset($this->sizes[$size]) && in_array($size, $this->section_sizes[$section]))
                    return $this->sizes[$size];
            }
        }
        elseif(!empty($section) && isset($this->section_sizes[$section]))
            return $this->section_sizes[$section];
       
        return null;
    }
    /**
     * Create directory where will save files 
     */
    private function createMediaDir()
    {
       File::createDir($this->dir);
    }
    /**
     * Set up settings for parent resize methods to exact resize image to
     * given dimensions. Use parent resize and crop methods
     * @param int $width width to resize
     * @param int $height height to resize
     * @param bool $fill {@todo} 
     * @param array $fill_colore {@todo} rgb collor
     * @return \MediaCommon return itself for chaining
     */
	public function exactResize($width, $height, $fill=false, $fill_colore=array(255, 255, 255))
	{
		if ($fill === false)
		{
			$origin_proportion = $this->image['width'] / $this->image['height'];
			$requested_proportion = $width / $height;

			$ext_proportion = abs($origin_proportion - $requested_proportion);

//			$corrected_origin_width = intval( $this->image['width'] + $this->image['width'] * ($ext_proportion / 2) );
//			$corrected_origin_height = intval( $this->image['height'] + $this->image['height'] * ($ext_proportion / 2) );

			$corrected_width = intval( $width + $width * ($ext_proportion / 2) );
			$corrected_height = intval( $height + $height * ($ext_proportion / 2) );



			if ($width > $height && $this->image['width'] > $this->image['height'])
			{
				$master = self::WIDTH;
			}
			elseif ($width < $height && $this->image['width'] < $this->image['height'])
			{
				$master = self::HEIGHT;
			}
			elseif ($width > $height && $this->image['width'] < $this->image['height'])
			{
				$master = self::WIDTH;
			}
			elseif ($width < $height && $this->image['width'] > $this->image['height'])
			{
				$master = self::HEIGHT;
			}
			elseif ($width == $height && $this->image['width'] < $this->image['height'])
			{
				$master = self::WIDTH;
			}
			elseif ($width == $height && $this->image['width'] > $this->image['height'])
			{
				$master = self::HEIGHT;
			}
			else
				$master = self::AUTO;

			$this->resize($corrected_width, $corrected_height, $master)->crop($width, $height);
		}
		else
		{
			
		}
		return $this;
	}
    /**
     * Create media files depending on its sizes of given section
     * @param string $section section name
     * @param bool $save_origin (optional d:true) flag to save original file
     */
    public final function createThumbnails($section, $save_origin=true)
    {
        $this->saveFiles($section, $save_origin);
    }
    /**
     * Create media files of given section without resizing
     * @param string $section section name
     * @param bool $save_origin (optional d:true) flag to save original file
     */
    public final function saveWithoutResize($section,$save_origin=true)
    {
        $this->saveFiles($section, $save_origin, false);
    }
    /**
     * Create media files depending on its sizes of given section
     * @param string $section section name
     * @param bool $save_origin (optional d:true) flag to save original file
     * @param type $with_resize 
     */
    private function saveFiles($section, $save_origin=true, $with_resize=true)
    {
        $this->createMediaDir();
        //save origin
        if($save_origin)
            $this->save($this->dir . DIRECTORY_SEPARATOR . $this->makeName(self::ORIGIN));
        // save other sizes
        if(isset($this->section_sizes[$section]))
        {        
            foreach($this->getSize($section) as $k=>$v)
            {
                $info = $this->formPresaveData($section,$k,$v);
                if($with_resize)
                    $this->exactResize($info['size']['w'], $info['size']['h'])->sharpen(20);
                $this->save($this->dir.DIRECTORY_SEPARATOR.$info['name']);
            }
        }
    }
    /**
     * Make filename and its sizes for given entity and section
     * @param string $s section name
     * @param string $k entity name, i.e. profile
     * @param array|string $v could be array if we set individual dimensions for entity
     * or could be a string of file name, and for that case function get size for that file
     * @return array
     */
    private function formPresaveData($s,$k,$v)
    {
        $size = $this->getSize($s, $v);
        if(is_array($v))
            $file_name = $this->makeName($k);
        else
            $file_name = $this->makeName($v);
        return array('size'=>$size,'name'=>$file_name);
    }
    /**
     * create file name to save
     * @param string $filename part of file name
     * @param string $suff (optional d:null) aditional suffics
     * @return string
     */
	private function makeName($filename, $suff=null)
	{
		return $filename.((isset($suff) && !empty($suff)) ? '_'.$suff : '').'.'.self::$ext;
	}
    /**
     * Set up class settings for next file, if previously
     * already deal with another file
     * @param string $file path to file
     */
	public final function next($file)
	{
		$n = $this->factory($file, $this->config);
        $this->image = $n->image;
	}
    /**
     * Set up common media url's and dir's for project
     */
    public static function mediaUrls()
    {
        App::controller()->img_dir = App::controller()->base_dir.self::GALLERY_DIR.DS.self::IMAGE_DIR.DS;
        App::controller()->img_url = App::controller()->domain_url.'/'.self::GALLERY_DIR.'/'.self::IMAGE_DIR.'/';

        App::view()->addDir('img', App::controller()->img_dir);
    }
    /**
     * Change recursively extension of the files
     * @param string $from from extension without "."
     * @param string $to to extension without "."
     * @param string $path optional start path
     * @return bool
     */
    public static function changeExt($from, $to, $path=null)
    {
        if(is_null($path))
        {
            $path = App::controller()->img_dir;
        }
        foreach(File::listing($path) as $dir)
        {
            if($dir != '.svn')
            {
                if(is_dir($path.DIRECTORY_SEPARATOR.$dir))
                {
                    self::changeExt($from, $to, $path.DIRECTORY_SEPARATOR.$dir);
                }
                elseif(strpos($dir, '.'.$from) !== false)
                {
                    File::rename($path.DIRECTORY_SEPARATOR.$dir, str_replace('.'.$from, '.'.$to, $path.DIRECTORY_SEPARATOR.$dir));
                }
            }
        }
        return true;
    }
    /**
     * Create thumbs for temporary uploades
     * @param string $file_name filename to save
     * @return bool
     */
    public function createTmpThumb($file_name)
    {
        foreach($this->getSize(self::TMP_DIR) as $k=>$v)
        {
            $info = $this->formPresaveData(self::TMP_DIR, $k, $v);
            $this->exactResize($info['size']['w'], $info['size']['h'])->sharpen(20);
            $this->save($this->dir.$file_name);
        }
        return true;
    }
}

/* End of file Media.php */
/* Location: ./class/Common/Media.php */
?>
