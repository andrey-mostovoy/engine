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

Loader::loadExtension('compressor.Compressor');

/**
 * <p>class CompressorJs.</p>
 * <p>Depending on {@see use} variable to compress js use:
 * if set gcompl  - Use Google Closure Compiler Service 
 * {@link http://code.google.com/intl/ru-RU/closure/compiler/docs/api-ref.html}
 * if set jsmin  - Use JsMin library
 * if set appropriate - try to use google closure compiler, and if some error
 * try to use jsmin library</p>
 * <p>Compilation flow: script check hash file, if exsist - 
 * return url to them, if not - call %host%/compressor/js url which 
 * call {@see doCompress} method</p>
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 */
class CompressorJs extends Compressor
{
    /**
     * Resource name
     * @var string
     */
    protected $resource = 'js';
    /**
     * Which compressor to use.
     * Can be gcompl, jsminplus, appropriate
     * @var string
     */
    private $use = 'appropriate';
    /**
     * url to service with compressor
     * @var string 
     */
    private $service_url = 'http://closure-compiler.appspot.com/compile';
    /**
     * Google Closure Compiler setting. Output format type.
     * Can be xml, json or text
     * @var string
     */
    private $output_format = 'text';
    /**
     * Google Closure Compiler setting. Output information scope.
     * {@see http://code.google.com/intl/ru-RU/closure/compiler/docs/api-ref.html}
     * @var string
     */
    private $output_info = 'compiled_code';
    /**
     * Google Closure Compiler setting. Compress level. 
     * Can be SIMPLE_OPTIMIZATIONS, ADVANCED_OPTIMIZATIONS, WHITESPACE_ONLY.
     * For more details see 
     * {@link http://code.google.com/intl/ru-RU/closure/compiler/docs/api-ref.html}
     * @var string 
     */
    private $compilation_level = 'SIMPLE_OPTIMIZATIONS';
    
    /**
     * Create compressed file
     * @return boolean true on success, false otherwise
     */
    public function doCompress()
    {
        if(!$this->restoreData())
            return false;
        
        if($this->use == 'jsminplus')
        {
            return $this->doCompressJsminplus();
        }
        elseif($this->use == 'gcompl')
        {
            return $this->doCompressGcompl();
        }
        elseif($this->use == 'appropriate')
        {
            return $this->doCompressAppropriate();
        }
        
    }
    /**
     * Create compressed file using JsMinPlus library
     * @return boolean true on success otherwise false
     */
    private function doCompressJsminplus()
    {
//        Loader::loadLib('compressor/js/jsmin');
        Loader::loadLib('compressor/js/jsminplus');
        
        $result = $content = '';
        foreach($this->url as &$v)
        {
            $content .= file_get_contents(App::request()->getBaseDir().$v);
        }
        unset($v);
        foreach($this->code as &$v)
        {
            $content .= trim(
                preg_replace(
                    array('/^(\<script.*\>)/i', '/\<\/script\>$/i'),
                    '', 
                    trim($v)
                )
            );
        }
        unset($v);
        
//        $result = JSMin::minify($content);
        $result = JSMinPlus::minify($content);
        
        if(empty($result))
        {
            trigger_error('Errors in JSMinPlus or result of compress is empty');
            return false;
        }
        // save to file
        return $this->saveMinifyFile($result);
    }
    /**
     * Create compressed file using Google Closure Compiler Service 
     * {@link http://code.google.com/intl/ru-RU/closure/compiler/docs/api-ref.html}
     * @return boolean true on success otherwise false
     */
    private function doCompressGcompl()
    {
        Loader::loadExtension('Curl');
        $curl = new Curl();
        $curl->setOption(CURLOPT_HTTPHEADER, array(
            'Content-type: application/x-www-form-urlencoded'
        ));
        $curl->addPostFields(array(
            'output_format'=>$this->output_format,
            'output_info'=>$this->output_info,
            'compilation_level'=>$this->compilation_level,
//                    'externs_url'=>urlencode(App::request()->getBaseUrl().App::view()->getCommonJsUrl().'/jquery-1.7.1.min.js'),
//                    'js_externs' => 'global',
        ));
        $url='';
        $code='';
        // generate urls for js files
        foreach($this->url as &$v)
        {
            $url .= 'code_url='.urlencode(App::request()->getBaseUrl().$v).'&';
        }
        unset($v);
        if(!empty($url))
        {
            $url = rtrim(substr($url, 9),'&');
            $curl->addPostField('code_url',$url);
        }
        // generate params with given pice of code
        foreach($this->code as &$v)
        {
            $tv = trim(
                preg_replace(
                    array('/^(\<script.*\>)/i', '/\<\/script\>$/i'),
                    '', 
                    trim($v)
                )
            );
            $code .= 'js_code='.urlencode($tv).'&';
        }
        unset($v);
        if(!empty($code))
        {
            $code = rtrim(substr($code, 8),'&');
            $curl->addPostField('js_code',$code);
        }
        // make request
        $result = $curl->post($this->service_url);
        if(!empty($result))
        {
            if('Error' == substr($result, 0, 5))
            {
                return false;
            }
            // save to file
            return $this->saveMinifyFile($result);
        }
        return false;
    }
    /**
     * Create compressed file using Google Closure Compiler Service 
     * {@link http://code.google.com/intl/ru-RU/closure/compiler/docs/api-ref.html}
     * If they fail try to use jsminplus library
     * @return boolean true on success otherwise false
     */
    private function doCompressAppropriate()
    {
        if(!($res = $this->doCompressGcompl()))
        {
            $res = $this->doCompressJsminplus();
        }
        return $res;
    }
    /**
     * Retrieav html of including resourses
     * @return string
     */
    public function output()
    {
        if(is_array($this->output))
        {
            $return = '';
            foreach($this->output as &$v)
            {
                if(isset($v['url']))
                {
                    $return .= $this->outputSingle($v['url']);
                }
                else
                {
                    $return .= $v['code'];
                }
            }
            return $return;
        }
        return $this->outputSingle($this->output);
    }
    /**
     * Retrieav html of including single resourses line
     * @param string $url
     * @return string 
     */
    public function outputSingle($url)
    {
        return '<script type="text/javascript" src="'.$url.'"></script>';
    }
}
/* End of file CompressorJs.php */
/* Location: ./class/extension/compressor/CompressorJs.php */
?>