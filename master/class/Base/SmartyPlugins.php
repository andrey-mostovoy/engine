<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * class Smarty plugins
 * Set up smarty user plugins
 *
 * @package		Base
 * @author		amostovoy
 */
abstract class BaseSmartyPlugins
{
    /**
     * function return additional plugins to register
     * to project. Format:
     * array(
     *      array(
     *          'type' => 'function',
     *          'name' => 'name_in_smarty_tpl',
     *          'callback' => 'functionInSmartyPluginsClass'
     *      ),
     *  ); 
     * type key is optional. {@see http://www.smarty.net/docs/en/api.register.plugin.tpl}
     * name key is a plugin name in smarty temlates
     * callback is a method name in SmartyPlugins class
     * @return array
     */
    abstract public function init();
    /**
     * Smarty function
     * Perform including of template element. All aditional params
     * will be assigned to use only inside included template.
     * Without them they will be unavailable
     * 
     * @param array $params
     *  - file - string
     *  - assign - (optional) flag to assign output to variable
     *  - only_path - (optional) flag to return just tpl path
     * @param object $template - smarty object
     * @example {include_element file=globalvars assign=tmp}
     * @return string fetched template
     * If no element found or no element name in params -> error will be triggered
     */
    public final function includeElement($params, $template)
    {
        if (!empty($params['file']))
        {
            if(($tpl = App::view()->getTemplateElement($params['file'])))
            {
                if(!empty($params['only_path']))
                {
                    if(isset($params['assign']) && !empty($params['assign']))
                    {
                        $template->assign($params['assign'], $tpl);
                        return '';
                    }
                    return $tpl;
                }
                // to work only inside included element
                unset($params['file']);
                $_template = clone $template;
                $_template->assign($params);
                $tpl = $_template->fetch($tpl);
                unset($_template);
                
                if(isset($params['assign']) && !empty($params['assign']))
                {
                    $template->assign($params['assign'], $tpl);
                    return '';
                }
                return $tpl;
            }
            else
            {
                throw new Exception('Element \''.$params['file'].'\' not found');
            }
        }
        else
        {
            throw new Exception('No element name');
        }
    }
    /**
     * Smarty function
     * Perform including of up level hierarchy element template.
     * All aditional params will be assigned to use only inside included template.
     * Without them they will be unavailable
     * 
     * @param array $params
     *  - level - int (optional d:1) level of hierarchy structure from down to up
     *  - assign - (optional) flag to assign output to variable
     * @param object $template - smarty object
     * @example {include_element level=1 assign=tmp}
     * @return string fetched template
     * If no element found  -> error will be triggered
     */
    public final function includeUpElement($params, $template)
    {
        $level = (isset($params['level']) ? intval($params['level']) : 1);

        $tpl = App::view()->getTemplateUpElementPath(
                $template->template_resource,
                $level
        );
        if($tpl)
        {
            // to work only inside included element
            unset($params['level']);
            $_template = clone $template;
            $_template->assign($params);
            $tpl = $_template->fetch($tpl);
            unset($_template);

            if(isset($params['assign']) && !empty($params['assign']))
            {
                $template->assign($params['assign'], $tpl);
                return '';
            }
            return $tpl;
        }
        else
        {
            throw new Exception('No up element for "'.$template->template_resource.'". ');
        }
    }
    /**
     * outputs js code for assigning variables
     * 
     * @param array $params array with given params
     *  - to    - to that js variable assign
     *  - from  - from that smarty variable take
     * @param object $template - smarty object
     * @example {globalvars to=global from=js_vars}
     * @return string
     */
    public final function globalvars($params, $template)
    {
        $from = $params['from'];
        $to = (empty($params['to']) ? 'global' : $params['to']);

        $from = $template->getTemplateVars($from);
        
        return serialize($from);
    }
    
    /**
     * return translated text. Language from transate always english,
     * to language translate set by configs. If no variable passed,
     * in translated text past given key. Passed inside tags smarty
     * variables will be parsed and translated
     * @example <p>
     * {lang var1=$var var2="Vasia"}
     *   Text to translate {$smarty_var}. Your name: ::var2, and you are ::var1
     * {/lang}
     * </p>
     * 
     * @param array $params array with params
     * @param string $content compiled tpl
     * @param Smarty $template smarty object
     * @param bool $repeat
     * @return string
     */
    public final function lang($params, $content, $template, &$repeat)
    {
        // only output on the closing tag
        if(!$repeat && isset($content))
        {
            if(!empty($params))
            {
                preg_match_all('/(::[\w\d]+)/i', $content, $nt_vars);

                if(!empty($nt_vars))
                {
                    foreach($nt_vars[0] as $k => $match)
                        $no_translate_vars[$k] = $match;
                    
                    $content = str_replace(
                            array_values($no_translate_vars),
                            array_map(
                                    create_function('$val', 'return "{".($val+1)."}";'),
                                    array_keys($no_translate_vars)),
                            $content);
                }
            }
            
            if('en' != App::lang()->getCurrentLang())
                $translation = App::lang()->translate($content);
            else
                $translation = $content;
           
            if(isset($no_translate_vars) && !empty($no_translate_vars))
            {
                foreach($no_translate_vars as $k => &$r)
                {
                    if(isset($params[($repl = str_replace('::', '', $r))]))
                        $r = $params[$repl];
                }
                // replace variables
                $translation = str_replace(
                        array_map(
                            create_function('$val', 'return "{".($val+1)."}";'),
                            array_keys($no_translate_vars)),
                        array_values($no_translate_vars),
                        $translation);
            }       
            return $translation;
        }
    }
    /**
     * Smarty function
     * Perform collect css url resourses for compression.
     * 
     * @param array $params
     *  - file - string
     *  - media - (optional) string of media type of css file
     * @param Smarty $template - smarty object
     * @example {css file="`$url.css`/style.css" media="print"}
     * @return string html code for paste or empty string
     */
    public final function compressResoursesCss($params, $template)
    {
        if (!empty($params['file']))
        {
            if(!isset($params['media']))
            {
                $params['media'] = 'screen';
            }
            if(Config::RESOURCE_COMPRESSION)
            {
                Loader::loadExtension('compressor.CompressorCss');
                Compressor::getInstance('css')->addUrl($params['file']);
                return '';
            }
            else
            {
                return '<link media="'.$params['media'].'" type="text/css" rel="stylesheet" href="'.$params['file'].'" />';
            }
        }
        else
        {
            throw new Exception('No element name');
        }
    }
    /**
     * Smarty block function
     * Perform collect css code resourses for compression.
     * Must be set before <style> tag
     * 
     * @param array $params array with params. no params handle inside
     * @param string $content compiled tpl
     * @param Smarty $template smarty object
     * @param bool $repeat
     * @example {css_code}<style>...</style>{/css_code}
     * @return string html code for paste
     */
    public final function compressResoursesCssCode($params, $content, $template, &$repeat)
    {
        // only output on the closing tag
        if(!$repeat && isset($content))
        {
            if(Config::RESOURCE_COMPRESSION)
            {
                Loader::loadExtension('compressor.CompressorCss');
                Compressor::getInstance('css')->addCode($content);
                return '';
            }
            else
            {
                return $content;
            }
        }
    }
    /**
     * Smarty function
     * Perform collect js url resourses for compression.
     * 
     * @param array $params
     *  - file - string
     * @param Smarty $template - smarty object
     * @example {js file="`$url.js`/index.js"}
     * @return string html code for paste or empty string
     */
    public final function compressResoursesJs($params, $template)
    {
        if (!empty($params['file']))
        {
            $params['compile'] = isset($params['compile']) ? $params['compile'] : true;
            if(Config::RESOURCE_COMPRESSION && $params['compile'])
            {
                Loader::loadExtension('compressor.CompressorJs');
                Compressor::getInstance('js')->addUrl($params['file']);
                return '';
            }
            else
            {
                return '<script type="text/javascript" src="'.$params['file'].'"></script>';
            }
        }
        else
        {
            throw new Exception('No element name');
        }
    }
    /**
     * Smarty block function
     * Perform collect js code resourses for compression.
     * Must be set before <script> tag
     * 
     * @param array $params array with params. no params handle inside
     * @param string $content compiled tpl
     * @param Smarty $template smarty object
     * @param bool $repeat
     * @example {js_code}<script>...</script>{/js_code}
     * @return string html code for paste
     */
    public final function compressResoursesJsCode($params, $content, $template, &$repeat)
    {
        // only output on the closing tag
        if(!$repeat && isset($content))
        {
            if(Config::RESOURCE_COMPRESSION)
            {
                Loader::loadExtension('compressor.CompressorJs');
                Compressor::getInstance('js')->addCode($content);
                return '';
            }
            else
            {
                return $content;
            }
        }
    }
    /**
     * Smarty function
     * Perform compress resourses and return string of include html
     * of compresed files or included.
     * 
     * @param array $params no params handle inside
     * @param Smarty $template - smarty object
     * @example {compress}
     * @return string html code for paste or empty string
     */
    public final function compressResourses($params, $template)
    {
        if(Config::RESOURCE_COMPRESSION)
        {
            Loader::loadExtension('compressor.Compressor');
            Compressor::compressAll();
            return Compressor::outputAll();
        }
        else
        {
            return '';
        }
    }
    
    public final function widget($params, $template)
    {
        if(!isset($params['type']))
        {
            throw new InternalException('empty \'type\' param in widget plugin');
        }
        
//        $template
//        $tpl = $template->fetch( App::view()->getTemplateElement('widget') );
        $tpl = $this->includeElement(
                array(
                    'file' => 'widget',
                    'widget_entry' => App::widget()->getWidget($params['type']),
                    'type' => $params['type']
                ),
                $template
        );
        return $tpl;
    }
    
    public final function includeByAjax($params, $template)
    {
        if(!isset($params['url']))
        {
            throw new InternalException('empty \'url\' param in include_ajax plugin');
        }
        $url = $params['url'];
        $id = 'js_'.str_replace(array('/'), '', $url);
        unset($params['url']);
        
        $data = '{';
        foreach($params as $k=>&$v)
        {
            $data .= '"'.$k.'":"'.$v.'",';
        }
        $data = rtrim($data, ',').'}';
        
        return '<div id="'.$id.'">
            <div style="padding-top:70px; text-align:center;">
                <img src="'.App::view()->getCommonImageUrl().'/ajax-loader.gif" alt="ajax-loader.gif"/>
            </div>
            <script type="text/javascript">
                $(document).ready(function(){
                    sendData(
                        "'.App::controller()->base_url.'/'.$url.'",
                        '.$data.',
                        function(r){
                            
                            $("#'.$id.'").replaceWith(r.content);
                            
                        },
                        "json"
                    );
                });
            </script></div>';
    }
}

/* End of file BaseSmartyPlugins.php */
/* Location: ./class/Base/SmartyPlugins.php */
?>