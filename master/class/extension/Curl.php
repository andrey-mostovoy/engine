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
 * class Curl
 * containing methods to deel with curl..
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 */
class Curl
{
    /**
     * cURL resource
     * @var handler
     */
    protected $handler;
    /**
     * received content
     * @var string
     */
    protected $content;
    /**
     * info about last transfer
     * @var mixed
     */
    protected $info;
    /**
     * curl options
     * {@link http://www.php.net/manual/ru/function.curl-setopt.php}
     * @var array
     */
    protected $options;
    /**
     * POST fields
     * @var array
     */
    protected $post_fields;
    /**
     * GET fields
     * @var array
     */
    protected $get_fields;
    /**
     * COOKIE
     * @var array
     */
    protected $cookie_fields;

    /**
     * class constructor. init all variables 
     */
    public function __construct() {
        $this->handler          = curl_init();  // init cURL resource
        $this->content          = '';           // init content
        $this->info             = array();      // info about last request
        $this->post_fields      = array();      // init POST fields
        $this->get_fields       = array();      // init GET fields
        $this->cookie_fields    = array();      // init COOKIE
        $this->options          = array();      // init options with some default values
        $this->setOptions(array(
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.220 Safari/535.1',
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_CONNECTTIMEOUT  => 30,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_MAXREDIRS       => 10,
        ));
    }
    /**
     * Apply optins from {@see options} array to current 
     * curl request
     * @return bool true on success, false otherwise
     */
    protected function applyOptions()
    {
        return curl_setopt_array($this->handler, $this->options);
//        foreach ($this->options as $key => $value) {
//            curl_setopt($this->handler, constant($key), $value);
//        }
    }
    /**
     * Add GET params to giben url
     * @param string $url
     * @return string
     */
    protected function addParamsToUrl($url)
    {
        $params = '';
        foreach ($this->get_fields as $key => $value) {
            $params .= $key.'='.rawurlencode($value).'&';
        }
        if ($params) {
            $url .= (strpos($url, '?') === false ? '?' : '&').$params;
        }
        return rtrim($url, '&');
    }
    /**
     * Add cookie option to current options
     */
    protected function applyCookie()
    {
        $cookie = '';
        foreach ($this->cookie_fields as $key => $value) {
            $cookie .= $key.'='.rawurlencode($value).'; ';
        }
        $this->setOption(CURLOPT_COOKIE, $cookie);
     }
     /**
      * Convert array of params with key=>value pairse to
      * string
      * @param array $fields
      * @return string
      */
    protected function fieldsToString($fields)
    {
         $params = '';
         foreach($fields as $key=>$value) {
             $params .= $key.'='.$value.'&';
         }
         return rtrim($params. '&');
    }
    /**
     * Add single option to request
     * {@link http://www.php.net/manual/ru/function.curl-setopt.php}
     * @param string $key
     * @param mixed $value 
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }
    /**
      * Add multiple options to request
      * {@link http://www.php.net/manual/ru/function.curl-setopt-array.php}
      * @param array $array key=>value pair
      */
    public function setOptions($array)
    {
        foreach($array as $k=>&$v)
        {
            $this->setOption($k,$v);
        }
    }
    /**
     * make curl get request with previously set up options
     * @param string $url
     * @return mixed result of request
     */
    public function get($url)
    {
        $this->setOptions(array(
            CURLOPT_URL     => $this->addParamsToUrl($url),
            CURLOPT_POST    => false
        ));
        $this->applyCookie();
        $this->applyOptions();
        $this->content = curl_exec($this->handler);
        $this->info = curl_getinfo($this->handler);
        return $this->content;
    }
    /**
     * make curl post request with previously set up options
     * @param string $url
     * @return mixed result of request
     */
    public function post($url)
    {
        $this->setOptions(array(
            CURLOPT_URL         => $this->addParamsToUrl($url),
            CURLOPT_POST        => true,
            CURLOPT_POSTFIELDS  => $this->fieldsToString($this->post_fields)
        ));
        $this->applyCookie();
        $this->applyOptions();
        $this->content = curl_exec($this->handler);
        $this->info = curl_getinfo($this->handler);
        return $this->content;
    }
    /**
     * Return result content of last request
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * Get status of last request
     * @return mixed
     */
    public function getStatus()
    {
        return isset($this->info['http_code']) ? $this->info['http_code'] : false;
    }
    /**
     * add single value to post fields of next request
     * @param string $key 
     * @param mixed $value 
     */
    public function addPostField($key, $value)
    {
        $this->post_fields[$key] = $value;
    }
    /**
     * add multiple values to post fields of next request
     * @param array $array key=>value pair
     */
    public function addPostFields($array)
    {
        $this->post_fields = array_merge($this->post_fields, $array);
    }
    /**
     * Clean up all post fields 
     */
    public function clearPostFields()
    {
        $this->post_fields = array();
    }
    /**
     * Retrieve all post fields that was set up
     * @return array
     */
    public function getPostFields()
    {
        return $this->post_fields;
    }
    /**
     * add single value to get fields of next request
     * @param string $key 
     * @param mixed $value 
     */
    public function addGetField($key, $value)
    {
        $this->get_fields[$key] = $value;
    }
    /**
     * add multiple values to get fields of next request
     * @param array $array key=>value pair
     */
    public function addGetFields($array)
    {
        $this->get_fields = array_merge($this->get_fields, $array);
    }
    /**
     * Clean up all get fields 
     */
    public function clearGetFields()
    {
        $this->get_fields = array();
    }
    /**
     * Retrieve all get fields that was set up
     * @return array
     */
    public function getGetFields()
    {
        return $this->get_fields;
    }
    /**
     * add single value to cookie fields of next request
     * @param string $key 
     * @param mixed $value 
     */
    public function addCookieField($key, $value)
    {
        $this->cookie_fields[$key] = $value;
    }
    /**
     * add multiple values to cookie fields of next request
     * @param array $array key=>value pair
     */
    public function addCookieFields($array)
    {
        $this->cookie_fields = array_merge($this->cookie_fields, $array);
    }
    /**
     * Clean up all cookie fields 
     */
    public function clearCookieFields()
    {
        $this->cookie_fields = array();
    }
    /**
     * Retrieve all cookie field that was set up
     * @return array
     */
    public function getCookieFields()
    {
        return $this->cookie_fields;
    }
}
/* End of file Curl.php */
/* Location: ./class/extension/Curl.php */
?>