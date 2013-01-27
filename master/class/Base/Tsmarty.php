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

Loader::LoadLib('Smarty/Smarty.class');
Loader::loadClass(array('Common_SmartyPlugins'));

/**
 * class Tsmarty
 * Set up main smarty variables
 *
 * @package		Base
 * @author		amostovoy
 */
class Tsmarty extends Smarty
{
	/**
	 * templates directory from project root
	 */
	const TEMPLATES_DIR = 'templates';
    /**
	 * compiled templates directory from project root
	 */
	const COMPILED_DIR = 'templates_c';
    /**
     * plugins dir name
     */
    const PLUGINS_DIR = 'smarty_plugins';
	/**
	 * config directory from project root
	 */
	const CONFIG_DIR = 'configs';
	/**
	 * cache directory from project root
	 */
	const CACHE_DIR = 'cache';
    /**
     * Smarty plagins object 
     * @var SmartyPlugins
     */
    protected $plugins = null;

    public function __construct($base_dir)
    {
        parent::__construct();
//        $this->_conf = $config;
        
        $this->plugins = new SmartyPlugins();

        $this->setTemplateDir($base_dir . self::TEMPLATES_DIR . DIRECTORY_SEPARATOR);
        $this->setCompileDir($base_dir . self::COMPILED_DIR . DIRECTORY_SEPARATOR);
        $this->setConfigDir($base_dir . self::CONFIG_DIR . DIRECTORY_SEPARATOR);
        $this->setCacheDir($this->getCompileDir() . self::CACHE_DIR . DIRECTORY_SEPARATOR);

        $this->addPluginsDir($base_dir . Loader::LIB_DIR . DIRECTORY_SEPARATOR . self::PLUGINS_DIR . DIRECTORY_SEPARATOR);

        // load filter to delete all whitespaces from html page
        $this->loadFilter('output', 'trimwhitespace');
        
        if(Config::DEBUG)
        {
//            $this->error_unassigned = true;
            $this->error_reporting = E_ALL & ~E_NOTICE;
            $this->compile_error = true;
            $this->caching = false;
//            $this->debugging = true;
        }
        else
        {
            $this->error_unassigned = false;
            $this->error_reporting = null;
            $this->compile_error = false;
            
            // this is use __call method
//            $this->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
//            $this->setCacheLifetime(-1);
//            $this->setDebugging(false);
        }
        
        //init smarty plugins (functions)
        $this->initSmartyPlugins();
    }
    
    /**
     * Return Smarty Plugin object instance
     * @return object
     */
    public final function plugin()
    {
        return $this->plugins;
    }
    /**
     * Register object
     * @param string $name name on register
     * @param object $obg object to register
     */
//    public final function registerObject($name, $obg)
//    {
//        $this->register_object($name, $obg);
//    }
    
    /**
     * Register function
     * 
     * @param string $name
     * @param mixed $callback
     */
    public final function registerFunction($name, $callback)
    {
        $this->registerPlugin('function', $name, $callback);
    }
    
    /**
     * Register block function
     * 
     * @param string $name
     * @param mixed $callback
     */
    public final function registerBlock($name, $callback)
    {
        $this->registerPlugin('block', $name, $callback);
    }
    
    /**
     * Init smarty plugins by default
     */
    private function initSmartyPlugins()
    {
        $this->registerFunction('globalvars', array($this->plugins, 'globalvars'));
        $this->registerFunction('include_up_element', array($this->plugins, 'includeUpElement'));
        $this->registerFunction('include_element', array($this->plugins, 'includeElement'));
        $this->registerFunction('include_ajax', array($this->plugins, 'includeByAjax'));
        $this->registerBlock('lang', array($this->plugins, 'lang'));
        
        $this->registerFunction('css', array($this->plugins, 'compressResoursesCss'));
        $this->registerBlock('css_code', array($this->plugins, 'compressResoursesCssCode'));
        $this->registerFunction('js', array($this->plugins, 'compressResoursesJs'));
        $this->registerBlock('js_code', array($this->plugins, 'compressResoursesJsCode'));
        $this->registerFunction('compress', array($this->plugins, 'compressResourses'));
        
//        $this->registerFunction('widget', array($this->plugins, 'widget'));
        
        if(is_array($plugins = $this->plugins->init()))
        {
            $this->initPlugins($plugins);
        }
    }
    
    /**
     * Register user plugins
     * @param array $plugins array with plugin info
     *      - type - type of register plugin, i.e. function, compile, block etc.
     *      - name - plugin name
     *      - callback - callback function or method
     */
    public function initPlugins(array $plugins)
    {
        foreach($plugins as $plugin)
        {
            $this->registerPlugin(
                    (isset($plugin['type']) ? $plugin['type'] : 'function'),
                    $plugin['name'],
                    (is_array($plugin['callback']) ? $plugin['callback'] : array($this->plugins, $plugin['callback']))
            );
        }
    }
}

/* End of file Tsmarty.php */
/* Location: ./class/Base/Tsmarty.php */
?>