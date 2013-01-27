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
 * class CompressorCss. Use 
 * {@link }
 * to compress css resourses. Compilation flow: script check hash file, if exsist - 
 * return url to them, if not - call %host%/compressor/css url which 
 * call {@see doCompress} method
 * 
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 */
class CompressorCss extends Compressor
{
    /**
     * Resource name
     * @var string
     */
    protected $resource = 'css';
    /**
     * Filters for compressor lib
     * @var array
     */
    private $filters = array(
                "ImportImports"					=> false, // default false
                "RemoveComments"				=> true, // default true
                "RemoveEmptyRulesets"			=> true, // default true
                "RemoveEmptyAtBlocks"			=> true, // default true
                "ConvertLevel3Properties"		=> false, // default false
                "ConvertLevel3AtKeyframes"		=> false, // default false
                "Variables"						=> true, // default true
                "RemoveLastDelarationSemiColon"	=> true, // default true
    );
    /**
     * Plugins for compressor lib
     * @var array
     */
	private $plugins = array(
                "Variables"						=> true, // default true
                "ConvertFontWeight"				=> true, // default false
                "ConvertHslColors"				=> true, // default false
                "ConvertRgbColors"				=> true, // default false
                "ConvertNamedColors"			=> true, // default false
                "CompressColorValues"			=> true, // default false
                "CompressUnitValues"			=> true, // default false
                "CompressExpressionValues"		=> true, // default false
    );

    /**
     * Create compressed file
     * @return boolean true on success, false otherwise
     */
    public function doCompress()
    {
        if(!$this->restoreData())
            return false;
        
        Loader::loadLib('compressor/css/cssmin-v3.0.1-minified');
        $result = $content = '';
        
        foreach($this->url as &$v)
        {
            $file_cont = file_get_contents(App::request()->getBaseDir().$v);

            $content .= str_replace('url(../', 
                    'url(../../..'
                    .substr($v, 0, strrpos($v, '/'))
                    .'/../', $file_cont);
        }
        unset($v);
        foreach($this->code as &$v)
        {
            $content .= trim(
                preg_replace(
                    array('/^(\<style.*\>)/i', '/\<\/style>$/i'),
                    '', 
                    trim($v)
                )
            );
        }
        unset($v);
        
        $result = CssMin::minify($content, $this->filters, $this->plugins);
        if(!CssMin::hasErrors() && !empty($result))
        {
            // save to file 
            return $this->saveMinifyFile($result);
        }
        else
        {
//                $error = CssMin::getErrors();
            trigger_error('Errors in CssMin or result of compress is empty');
            return false;
        }
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
        return '<link media="screen" type="text/css" rel="stylesheet" href="'.$url.'" />';
    }
}
/* End of file CompressorCss.php */
/* Location: ./class/extension/compressor/CompressorCss.php */
?>