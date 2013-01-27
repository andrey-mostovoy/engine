<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

/***************************************************
 *             DO NOT CHANGE                       *
 ***************************************************/

/**
 * class Compressor. Create compressed file of resourses, retrieve url to
 * that file or return stack with including files
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 */
abstract class Compressor
{
    /**
     * Resource name
     * @var string
     */
    protected $resource;
    /**
     * Resource file name for lock
     * @var string
     */
    private $resource_lock;
    /**
     * Cache dir depend on resource
     * @var string
     */
    protected $c_dir;
    /**
     * Cache url depend on resource
     * @var string
     */
    protected $c_url;
    /**
     * queue of file or code including
     * @var array
     */
    protected $stack = array();
    /**
     * array with url resourses to compress
     * @var array
     */
    protected $url = array();
    /**
     * array with code resourses to compress
     * @var array
     */
    protected $code = array();
    /**
     * url to compressed file
     * @var string
     */
    protected $hashfile = '';
    /**
     * output string. Depend on compressor class. string with including of 
     * compressed file or array with all including files
     * @var string|array
     */
    protected $output;
    /**
     * Class instance
     * @var Compressor
     */
    private static $instances;

    /**
     * class constructor. init all variables 
     */
    private function __construct()
    {
        $this->resource_lock = $this->resource.'_lock';
        
        $this->c_dir = App::view()->getCacheDir() . $this->resource . DS;
        $this->c_url = App::view()->getCacheUrl() . '/' . $this->resource . '/';
        
        if(!File::checkExist($this->c_dir))
        {
            File::createDir($this->c_dir);
        }
    }
    /**
     * Retrieve instance
     * @param string $c compressor type
     * @return Compressor
     */
    public static function getInstance($c)
    {
        if(!isset(self::$instances[$c]) || self::$instances[$c] === null)
        {
            $class = 'Compressor'.ucfirst($c);
            self::$instances[$c] = new $class();
        }
        return self::$instances[$c];
    }
    /**
     * Add resource url to compress
     * @param string $url 
     */
    public function addUrl($url)
    {
        if(!in_array($url,$this->url) && file_exists(App::request()->getBaseDir().$url))
        {
            $this->url[] = $url;
            $this->stack[] = 'url';
        }
    }
    /**
     * Add resource code pice to compress
     * @param string $code 
     */
    public function addCode($code)
    {
        if(!empty($code))
        {
            $this->code[] = $code;
            $this->stack[] = 'code';
        }
    }
    /**
     * Create hash file name
     */
    protected function createHash()
    {
        $len=0;
        for($i=0;$i<count($this->code);$i++)
        {
            $len += strlen($this->code[$i]);
        }
        $this->hashfile = md5(implode(';', $this->url).$len);
    }
    /**
     * Retrieve compressed file url or array with included resourses
     * and store it inside output variable
     * @return bool if nothing to compress return false
     */
    public function compress()
    {
        if(!empty($this->url) || !empty($this->code))
        {
            //create hash for file
            $this->createHash();

            //if compiled file exsist just return url to them, otherwise compile
            if(!file_exists($this->c_dir.$this->hashfile.'.'.$this->resource))
            {
                // run compilation proccess
                $this->runCompile();
                
                // array with included resourses
                $this->output = $this->returnStack();
            }
            else
            {
                // url to hash file
                $this->output = $this->c_url.$this->hashfile.'.'.$this->resource;
            }
            return true;
        }
        return false;
    }
    /**
     * Run compilation proccess like another process. For save time and 
     * do not wait response. Call %host%/compressor/%action% url of same
     * project. Action base on resource name stored in child class
     * @return boolean if some errors return false
     */
    private function runCompile()
    {
        // store settings to session
//        $this->saveToSession();
//
//        $opts = array(
//            'http'=>array(
//                'header'=>"Cookie: ".http_build_query($_COOKIE,null,'; ')."\r\n",
//                'timeout' => 0,
//                'ignore_errors' => true,
//            )
//        );
//
//        $context = stream_context_create($opts);
//        @file_get_contents(App::request()->getBaseUrl().'/compressor/'.$this->resource, false, $context);
//        return true;
        
        // if lock file exsist - we already start compile proccess. just return
        if(file_exists($this->c_dir.$this->hashfile.'.'.$this->resource_lock))
        {
            return true;
        }
        // create http query
        $data_url = http_build_query(array(
            'compressor' => serialize(array(
                $this->resource => array(
                    'url' => $this->url,
                    'code' => $this->code,
                    'hashfile' => $this->hashfile,
                ),
            )
        )));
        // collect data to make post request to url on server
        $data_len = strlen($data_url);
        $opts = array(
            'http'=>array(
                'method'=>'POST',
                'header'=>array(
//                    'Cookie: '.http_build_query($_COOKIE,null,'; '),
//                    'Connection: close',
                    'Content-type: application/x-www-form-urlencoded',
                    'Content-Length: '.$data_len,
                ),
                'timeout' => 0,
                'content'=>$data_url,
                'ignore_errors' => true,
            )
        );
        $context = stream_context_create($opts);
        // create lock file
        file_put_contents(
                $this->c_dir.$this->hashfile.'.'.$this->resource_lock,
                $data_url);
        // make post request
        @file_get_contents(
                App::request()->getBaseUrl().'/compressor/'.$this->resource,
                false,
                $context);
        return true;
    }
    /**
     * Save info to session. For compress action in separate proccess
     */
//    protected function saveToSession()
//    {
//        $_SESSION['compressor'][$this->resource]['url'] = $this->url;
//        $_SESSION['compressor'][$this->resource]['code'] = $this->code;
//        $_SESSION['compressor'][$this->resource]['hashfile'] = $this->hashfile;
//    }
    /**
     * Restore info from post data
     * @return boolean true on success, false otherwise
     */
    protected function restoreData()
    {
//        if(isset($_SESSION['compressor'][$this->resource]))
//        {
//            $this->url = $_SESSION['compressor'][$this->resource]['url'];
//            $this->code = $_SESSION['compressor'][$this->resource]['code'];
//            $this->hashfile = $_SESSION['compressor'][$this->resource]['hashfile'];
////            unset($_SESSION['compressor'][$this->resource]);
//        }
//        return true;
        
        $arr = unserialize(App::request()->getPost('compressor', App::request()->getGet('compressor', null, Request::FILTER_STRING_CLEAR), Request::FILTER_STRING_CLEAR));
        if(!empty($arr[$this->resource]))
        {
            $this->url = $arr[$this->resource]['url'];
            $this->code = $arr[$this->resource]['code'];
            $this->hashfile = $arr[$this->resource]['hashfile'];
            return true;
        }
        return false;
    }
    /**
     * Save compiled minify file and delete lock file
     * @param string $result result to save
     * @return bool true on success or false on fail
     */
    protected function saveMinifyFile($result)
    {
        //rename lock file
        File::rename(
                $this->c_dir.$this->hashfile.'.'.$this->resource_lock,
                $this->c_dir.$this->hashfile.'.'.$this->resource
        );
        // save to file 
        $r = file_put_contents($this->c_dir.$this->hashfile.'.'.$this->resource, $result);
        return $r ? true : false;
    }
    /**
     * Retrieav stack of including
     * @return array
     */
    protected function returnStack()
    {
        reset($this->url);
        reset($this->code);
        $return = array();
        foreach($this->stack as &$v)
        {
            if($v == 'url')
            {
                $return[] = array('url'=>current($this->url));
                next($this->url);
            }
            else
            {
                $return[] = array('code'=>current($this->code));
                next($this->code);
            }
        }
        return $return;
    }
    /**
     * Retrieav html of including resourses
     * @return string
     */
    abstract public function output();
    /**
     * Retrieav html of including single resourses line
     * @return string 
     */
    abstract public function outputSingle($url);
    /**
     * Compress all instances 
     */
    public static function compressAll()
    {
        foreach(self::$instances as &$i)
        {
            $i->compress();
        }
    }
    /**
     * Output from all instances
     * @return string
     */
    public static function outputAll()
    {
        $return = '';
        foreach(self::$instances as &$i)
        {
            $return .= $i->output();
        }
        return $return;
    }
}
/* End of file Compressor.php */
/* Location: ./class/extension/compressor/Compressor.php */
?>