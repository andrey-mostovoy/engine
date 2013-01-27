<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.36
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass(array('Base_Tsmarty'));

/**
 * class View
 * stores variables that will be assigned to smarty variables
 * on display action
 * also stores layout and template names
 *
 * @package		Base
 * @author		amostovoy
 */
class View extends Tsmarty
{
	/**
	 * View dir in template dir
	 */
	const VIEW_DIR = 'views';
	/**
	 * Template dir in templates smarty dir
	 */
	const TEMPLATE_DIR = 'default';
    /**
     * Skins dir, containing css and images
     */
    const SKINS_DIR = 'skins';
	/**
	 * Sub dir in views dir, i.e. module tpl dir
	 */
	const TEMPLATE_SUB_DIR = 'index';
	/**
	 * Dir in views dir for common elements
	 */
	const COMMON_TEMPLATE_DIR = 'common';
	/**
	 * default layout name for site
	 */
	const LAYOUT = 'layout';
	/**
	 * default template for site
	 */
	const TEMPLATE = 'index';
	/**
	 * dir for template elements as menu etc...
	 */
	const ELEMENTS_DIR = 'elements';
	/**
	 * directory insite templates directory with emails templates
	 */
	const EMAILS_TEMPLATE_DIR = 'emails';
	/**
	 * directory insite templates directory with other templates
	 */
	const OTHER_TEMPLATE_DIR = 'other';
    /**
     * css dir
     */
    const CSS_DIR = 'css';
    /**
     * js dir
     */
    const JS_DIR = 'js';
    /**
     * images dir
     */
    const IMAGE_DIR = 'images';
    /**
     * session key name
     */
    const SESS_NAME = 'view';
    /**
     * sub directory of project
     * @var string
     */
    private $sub_dir = '';
	/**
	 * Location for overloaded data.
	 * @var array
	 */
	private $overloaded_data = array();
	/**
	 * array of display messages
     * - error
     *      - general
     *      - form
     * - success
     * - notice
	 * @var array
	 */
	private $messages = array();
	/**
	 * Tsmarty class instance
	 * @var Tsmarty
	 */
	public $_tsmarty  = null;
	/**
	 * current template dir
	 * @var string
	 */
	private $_template_dir = null;
    /**
	 * current sub dir
	 * @var strring
	 */
	private $_template_sub_dir = null;
	/**
	 * current site layout
	 * @var string
	 */
	private $_layout = null;
	/**
	 * current site template
	 * @var string
	 */
	private $_template = null;
	/**
	 * array with json data to send to browser as ajax responce
	 * @var array
	 */
	private $_json = array();
	/**
	 * special subdir name for separated site parts
	 * @var string
	 */
	private $_special_dir = '';
    /**
     * array with lang variables need to send to browser
     * @var array
     */
    private $_js_lang_vars = array();
    /**
     * array with js variables need to send to browser
     * @var array
     */
    private $_js_vars = array();

    // new variables

    /**
     * view object
     * @var View
     */
    private static $instance = null;
    /**
     * current module name (also directory name in view dir)
     * @var string
     */
    private $module = null;
    /**
     * current module sections, i.e. active in that moment (also directories in module dir)
     * @var array
     */
    private $module_sections = array();
    /**
     * use current section layout or not
     * @var bool
     */
    private $use_section_layout = false;


	/**
	 * class construct
	 * fills class variables and create Tsmarty instance
	 */
	public function __construct()
	{
        if(!is_null(self::$instance))
        {
            trigger_error('Attempt to create view object when already created one');
            return self::$instance;
        }
        
        $special_dir = App::request()->getDir();
        $controller = App::request()->getControllerName();
        $template = App::request()->getActionName();
                
        parent::__construct(App::request()->getBaseDir());
        // new construct
        // @todo
        $this->sub_dir = App::request()->getSubdomain();
        $this->module_sections = explode(DS, substr($special_dir, 0, -1));
        $this->module = array_shift($this->module_sections);
        if(empty($this->module))
            $this->module = Request::FRONTEND_DIR;
        $this->_special_dir = rtrim($this->module . DS . implode(DS, $this->module_sections), DS);

//		$this->_special_dir = substr($special_dir, 0, -1);
//        $this->sub_dir = $sub_dir; 
		$this->_template_dir = defined('Config::TEMPLATE_DIR') ? Config::TEMPLATE_DIR : self::TEMPLATE_DIR;
		$this->_template_sub_dir = $special_dir . (empty($controller) ? self::TEMPLATE_SUB_DIR : strtolower($controller));
//        $this->_layout = $this->getLayout();
		$this->_template = empty($template) ? self::TEMPLATE : strtolower($template);
        $this->setLayout($this->getLayout());
                
//		$this->_tsmarty = new Tsmarty($base_dir);

        // set layout for separeted site part or use default site layout
//        if ( !empty($this->module) && 
//             is_file( $this->getTemplatePath() . $this->module . '.tpl' ) )
//		{
//			$this->setLayout($this->module);
//		}

        $this->addJsLangVar(array());
        $this->addJsVar(empty($_SESSION['js_vars'])?array():$_SESSION['js_vars']);
        unset($_SESSION['js_vars']);
        
        $this->getTemplateUrl();
        $this->getModuleTemplateUrl();
        $this->getElementsTemplateUrl();
        $this->getCssUrl();
        $this->getCommonCssUrl();
        $this->getJsUrl();
        $this->getCommonJsUrl();
        $this->getImageUrl();
        $this->getCommonImageUrl();
        
        if(!empty($_SESSION[self::SESS_NAME]['messages']))
        {
            $this->messages = $_SESSION[self::SESS_NAME]['messages'];
            unset($_SESSION[self::SESS_NAME]['messages']);
        }
//        self::$instance = $this;
	}
	
    /**
	 * Return instance of View class
	 * @return View
	 * @static
	 */
    public static function getInstance()
    {
        self::$instance === null and self::$instance = new self();
		return self::$instance;
    }
    
	/**
	 * set element of overloaded_data array
	 *
	 * @param string $param
	 * @param mixed $value
	 */
	public final function __set($param, $value) { $this->overloaded_data[$param] = $value; }

	/**
	 * get element of overloaded_data array
	 * @param strting $param key of element if overloaded_data array
	 * @return mixed value of element if overloaded_data array
	 */
	public final function &__get($param)
	{
		if ( !isset($this->overloaded_data[$param]) )
        {
			trigger_error(__CLASS__.' variable \''.$param.'\' not exist in overloaded_data');
            $r=null;
            return $r;
        }
		return $this->overloaded_data[$param];
	}

	/**
	 * Check for exist element of overloaded_data array
	 * @param string $index key of element of overloaded_data array
	 * @return boolean return true if element is set, false otherwise
	 */
	public final function __isset($index)
	{
		return isset($this->overloaded_data[$index]);
	}

    /**
	 * function destroy an element of the overloaded_data array
	 * @param string $name
	 */
	public final function __unset($name) { unset($this->overloaded_data[$name]); }

	/**
	 * Return current site template name
	 * @return string
	 */
	public final function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * Set current site template name
	 * @param string $template
	 */
	public final function setTemplate($template)
	{
		$this->_template = $template;
	}

	/**
	 * Set path to current template directory
	 * @param string $dir path from smarty templates directory
	 */
//	public final function setTemplateDir($dir)
//	{
//		$this->_template_dir = $dir;
//	}

	/**
	 * Return current template directory
	 * @return string
	 */
	public final function getTemplatesDir()
	{
		return $this->_template_dir;
	}

	/**
	 * Set Template sub directory
	 * @param string $dir path from views dir in template dir
	 */
	public final function setTemplateSubDir($dir)
	{
		$this->_template_sub_dir = File::getPath($dir); 
	}

	/**
	 * Return current template sub dir
	 * @return string
	 */
	public final function getTemplateSubDir()
	{
		return $this->_template_sub_dir;
	}

//	public final function getLayout(){return $this->_layout;}

	/**
	 * Set site layout
	 * @param string $layout file name in template dir
	 */
	public final function setLayout($layout)
	{
		!empty($layout) and $this->_layout = $layout;
	}
    /**
     * get layout tpl file. by default used layout.tpl from 
     * views/common dir
     * @return string|boolean 
     */
    public final function getLayout()
    {
        if($this->use_section_layout)
        {
            $parts = $this->module_sections;
        }
        $parts[] = $this->module;
        $parts[] = self::COMMON_TEMPLATE_DIR;
        
        $finded = true;

        do {
            if(empty($parts))
            {
                $finded = false;
                break;
            }

            $v = array_shift($parts);

            $layout_url = self::VIEW_DIR . DS .$v. DS . self::LAYOUT;
        } while(!is_file($this->getTemplatePath(). $layout_url . '.tpl'));

		return $finded ? $layout_url : false;
    }
	/**
	 * Get template dir path
	 * @return string 
	 */
	public final function getTemplatePath()
	{
		return $this->getTemplateDir(0) . $this->getTemplatesDir() . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get template url path
	 * @return string
	 */
	public final function getTemplateUrl()
	{
        return $this->_getUrl('template');
	}
    /**
	 * Get template for current module url path
	 * @return string
	 */
	public final function getModuleTemplateUrl()
	{
        return $this->_getUrl('module_template');
	}
    /**
	 * Get template for current elements path url path
	 * @return string
	 */
	public final function getElementsTemplateUrl()
	{
        return $this->_getUrl('elements_template');
	}

    /**
     * Return url to css
     * @return string
     */
    public final function getCssUrl()
    {
        return $this->_getUrl('css');
    }
    /**
     * Return url to common css
     * @return string
     */
    public final function getCommonCssUrl()
    {
        return $this->_getUrl('common_css');
    }

    /**
     * Return url to js
     * @return string
     */
    public final function getJsUrl()
    {
        return $this->_getUrl('js');
    }
    /**
     * Return url to common js
     * @return string
     */
    public final function getCommonJsUrl()
    {
        return $this->_getUrl('common_js');
    }

    /**
     * Return url to image
     * @return string
     */
    public final function getImageUrl()
    {
        return $this->_getUrl('image');
    }
    /**
     * Return url to image
     * @return string
     */
    public final function getCommonImageUrl()
    {
        return $this->_getUrl('common_image');
    }
    /**
     * Return url to cache
     * @return string
     */
    public final function getCacheUrl()
    {
        return $this->_getUrl('cache');
    }

    /**
     * Return formated url and add it to url array
     * @param string $type allow template, css, js, image
     * @return string|bool return false on incorrect parameters
     */
    private function _getUrl($type)
    {
        $url = $this->sub_dir.'/'.Tsmarty::TEMPLATES_DIR.'/'.$this->_template_dir;
        switch($type)
        {
            case 'css':
                $url .= '/' . self::SKINS_DIR . (!empty($this->module) ? '/'.$this->module : '') . '/' . self::CSS_DIR;
            break;
            case 'common_css':
                $url .= '/' . self::SKINS_DIR . '/' . self::COMMON_TEMPLATE_DIR . '/' . self::CSS_DIR;
            break;
            case 'js':
                $url .= '/' . self::JS_DIR . (!empty($this->module) ? '/'.$this->module : '');
            break;
            case 'common_js':
                $url .= '/' . self::JS_DIR;
            break;
            case 'cache':
                $url = $this->sub_dir.'/'.Tsmarty::COMPILED_DIR.'/'.Tsmarty::CACHE_DIR;
            break;
            case 'image':
                $url .= '/' . self::SKINS_DIR . (!empty($this->module) ? '/'.$this->module : '') . '/' . self::IMAGE_DIR;
            break;
            case 'common_image':
                $url .= '/' . self::SKINS_DIR . '/' . self::COMMON_TEMPLATE_DIR . '/' . self::IMAGE_DIR;
            break;
            case 'template':
            break;
            case 'module_template':
                $url .= '/' . self::VIEW_DIR . '/' . $this->_template_sub_dir;
            break;
            case 'elements_template':
                $url .= '/' . self::VIEW_DIR . '/' . $this->_template_sub_dir . '/' . self::ELEMENTS_DIR;
            break;
            default:
                return false;
        }
        return $this->addUrl($type, $url);
    }

    /**
     * add new url to url array
     * @param string $element key name for new url
     * @param string $value url
     * @return string
     */
    public final function addUrl($element, $value)
    {
        return $this->_add('url', $element, $value);
    }

    /**
     * add new image url to img array
     * @param string $element key name for new url
     * @param string $value url
     * @return string
     */
    public final function addImg($element, $value)
    {
        return $this->_add('img', $element, $value);
    }

    /**
     * add new tpl to tpl array
     * @param string $element key name for new tpl
     * @param string $value tpl path
     * @return string
     */
    public final function addTpl($element, $value)
    {
        return $this->_add('tpl', $element, $value);
    }
    
    /**
     * Get element tpl
     * 
     * @param string $element
     * @return string|null
     */
    public final function getTpl($element)
    {
        return $this->_get('tpl', $element);
    }
    
    /**
     * add new dir path to dir path array
     * @param string $element key name for new dir path
     * @param string $value dir path
     * @return string
     */
    public final function addDir($element, $value)
    {
        return $this->_add('dir', $element, $value);
    }
    
    /**
     * add new element to specific $type array
     * @param string $type array to insert. tpl, url, dir...
     * @param string $element key array
     * @param string $value array value
     * @return string
     */
    private function _add($type, $element, $value)
    {
        if(!isset($this->$type))
        {
            $this->$type = array();
        }
        $this->$type = array_merge($this->$type, array($element => $value));
        return $value;
    }
    
    /**
     * get element from specific $type array
     * @param string $type name of array from that need element
     * @param string $element key array
     * @return mixed return array element
     */
    private function _get($type, $element)
    {
        return (isset($this->$type) && isset($this->$type[$element])) ? $this->$type[$element] : null;
    }

	/**
	 * get path to element template
	 * @param string	$element	element template name
	 * @param bool		$short_path	if true return short variant of path (element_dir/element_name)
	 * @return mixed	return path to template or false on error if no file exist
	 */
	public final function getTemplateElement($element, $short_path = false)
	{
        if (($tpl = $this->getTpl($element)))
        {
            return $tpl;
        }
        else
        {
            return $this->addTpl($element, $this->_getTemplateElementPath($element, $short_path));
        }
	}

	/**
	 * get path to element template
	 * @param string	$element	element template name
	 * @param bool		$short_path	if true return short variant of path (element_dir/element_name)
	 * @return mixed	return path to template or false on error if no file exist
	 */
	private function _getTemplateElementPath($element, $short_path = false)
	{
        if ($short_path)
		{ // this is short version of path for example "elements/menu.tpl"
			$element_url = self::ELEMENTS_DIR . DIRECTORY_SEPARATOR . $element;
			if ( ! is_file( $this->getTemplatePath().
								self::VIEW_DIR . DIRECTORY_SEPARATOR .
                                $this->_template_sub_dir . DIRECTORY_SEPARATOR .
                                $element_url.'.tpl')
				)
				$element_url = false;
		}
		else
		{
            $finded = true;
            $parts = explode(DS, $this->_template_sub_dir);
            $parts[] = '';
            
            do {
                if(empty($parts))
                {
                    $finded = false;
                    break;
                }
                
                $v = array_pop($parts);

                $element_url = self::VIEW_DIR . DS
                            . (!empty($parts) ? implode(DS, $parts) . DS : '')
                            . (('' != $v) ? self::COMMON_TEMPLATE_DIR . DS : '')
                            . self::ELEMENTS_DIR . DS
                            . $element.'.tpl';

            } while(!is_file($this->getTemplatePath() . $element_url));
                    
            if($finded)
            {
                $element_url = Tsmarty::TEMPLATES_DIR .DS. $this->getTemplatesDir() .DS. $element_url;
            } 
		}
		return $element_url;
	}
    
    /**
     * get path to up level hierarchy element template
     * @param string $path path to current level element template
     * @param int $level level of hierarchy structure from down to up
     * @return boolean|string path to up level template or false on failure
     */
	public function getTemplateUpElementPath($path, $level=1)
	{
        $element = substr(
            $path,
            strrpos($path, DS)+1,
            strrpos($path, '.')-strrpos($path, DS)-1
        );

        $upls = array();
        $parts = explode(DS, $this->_template_sub_dir);
        $parts[] = '';

        do {
            if(empty($parts)) break;

            $v = array_pop($parts);

            $element_url = self::VIEW_DIR . DS
                        . (!empty($parts) ? implode(DS, $parts) . DS : '')
                        . (('' != $v) ? self::COMMON_TEMPLATE_DIR . DS : '')
                        . self::ELEMENTS_DIR . DS
                        . $element.'.tpl';

            if(is_file($this->getTemplatePath() . $element_url))
            {
                if(false !== strpos($path, $element_url))
                {
                    $upls=array();
                }
                else
                {
                    $upls[] = $element_url;
                }
            }
        } while(!empty($parts));

        if(isset($upls[$level-1]))
        {
            return Tsmarty::TEMPLATES_DIR .DS. $this->getTemplatesDir() .DS. $upls[$level-1];
        }
		return false;
	}

	/**
	 * set menu_tpl var with path to menu file for current controller or common if no file exist
     * @return string
	 */
	private function _setMenuTemplate()
	{
		return $this->getTemplateElement('menu');
	}
	
	/**
	 * set error_tpl var with path to common error template
     * @return string
	 */
	private function _setErrorTemplate()
	{
		return $this->getTemplateElement('error');
	}

	/**
	 * set message_tpl var with path to common message template
     * @return string
	 */
	private function _setMessageTemplate()
	{
		return $this->getTemplateElement('message');
	}

	/**
	 * set paging_tpl var with path to common paging template
     * @return string
	 */
	private function _setPagingTemplate()
	{
		return $this->getTemplateElement('paging');
	}

	/**
	 * check for display errors and return true if find it
	 * else return false
	 * @return boolean
	 */
	public final function isErrors()
	{
		if ( !empty($this->messages['error']['general']) || !empty($this->messages['error']['form']) )
			return true;
		return false;
	}

    /**
	 * check for display messages and return true if find it
	 * else return false
	 * @return boolean
	 */
	public final function isMessages()
	{
		if ( !empty($this->messages) )
			return true;
		return false;
	}
    
	/**
	 * Assign all from overloaded_data array into smarty variables,
	 * executes & returns or displays the template results.
     * Use common template if excist if can't find file with current view
	 *
	 * @param boolean $display if true method will display tempate result else return string with result
     * @param bool $use_layout (optional d:true) indicate use layout or not
	 * @return mixed 
	 */
	public final function printTemplate($display=true, $use_layout=true)
	{
		$content = $this->getTemplatePath()
                . self::VIEW_DIR . DIRECTORY_SEPARATOR
                . $this->_template_sub_dir . DIRECTORY_SEPARATOR
                . $this->_template . '.tpl';

        if(!is_file($content))
        {
            $parts = explode(DS, $this->_template_sub_dir);

            do {
                if(empty($parts))
                    break;
                array_pop($parts);
                $content = $this->getTemplatePath()
                        . self::VIEW_DIR . DS
                        . (!empty($parts) ? implode(DS,$parts).DS : '')
                        . self::COMMON_TEMPLATE_DIR . DS
                        . $this->_template . '.tpl';
            } while(!is_file($content));
        }
        if($this->use_section_layout)
        {
            $this->addTpl('section_content', $content);
            $content = $this->getTemplatePath()
                    . self::VIEW_DIR . DIRECTORY_SEPARATOR
                    . $this->_template_sub_dir . DIRECTORY_SEPARATOR
                    . 'layout.tpl';
            if ( ! is_file($content))
            {
                $content = $this->getTemplatePath()
                        . self::VIEW_DIR . DIRECTORY_SEPARATOR
                        . $this->_special_dir . DIRECTORY_SEPARATOR
                        . 'layout.tpl';
            }
        }
		return $this->_print($content, ($use_layout ? $this->_layout : ''), $display);
	}

	/**
	 * executes & returns or displays the template results
	 *
	 * @param string $content       template file with content data
	 * @param string $layout        (optional d:'') template file for layout
	 * @param bool	 $display       (optional d:true) indicate display result on page or return from method
	 * @param bool	 $assign_all    (optional d:true) indicate that need assign common variables such as errors, menu_tpl etc..
	 * @return mixed
	 */
	private function _print($content, $layout='', $display=true, $assign_all=true)
	{
        File::checkExist($content, true);

		if ($display && !empty($layout))
		{
            $this->addTpl('content', $content);
            File::checkExist( ($template = $this->getTemplatePath().$layout.'.tpl'), true);
		}
		else
        {
			$template = $content;
        }

		if ($assign_all)
		{
			$this->_setCommonParams();
		}
		$this->_assignParams();
        
//        if(Config::DEBUG === 0)
//            $this->cache_id = str_replace(array($this->template_dir.self::TEMPLATE_DIR.DS.self::VIEW_DIR.DS, DS), array('', '|'), dirname($content));
//        else
//            $this->cache_id = null;

//		return $this->fetch($template,$this->cache_id,null,null,$display);
		return $this->fetch($template,null,null,null,$display);
	}

//    public function fetch($template)
//    {
//        return $this->_tsmarty->fetch($template);
//    }
    
//    public function assign($var)
//    {
//        $this->_tsmarty->assign($var);
//    }
	/**
	 * Return compiled smarty template
	 * 
	 * @param string $template template name to fetch
     * @param bool $use_layout (optional d:false) indicate use layout or not
	 * @return string return compiled template
	 */
	public final function fetchTemplate($template='', $use_layout=false)
	{
		if (!empty($template))
			$this->setTemplate($template);
		return $this->printTemplate(false, $use_layout);
	}
    
    /**
	 * Return compiled element smarty template
	 * 
	 * @param string $template template name to fetch
	 * @return string return compiled template
	 */
	public final function fetchElementTemplate($template='')
	{
        if(empty($template))
            $template = $this->_template;
        $content = App::controller()->base_dir . $this->_getTemplateElementPath($template);
		return $this->_print($content, '', false, false);
	}

	/**
	 * compile template for email
	 * @param string $email			template email filename
	 * @param bool	 $use_layout	(optional d:true) indicate use layout or not
	 * @return string return compiled template
	 */
	public final function fetchEmailTemplate($email, $use_layout = true)
	{
		$content = $this->getTemplatePath() . self::EMAILS_TEMPLATE_DIR . DIRECTORY_SEPARATOR . $email.'.tpl';
		return $this->_print($content, ($use_layout ? self::EMAILS_TEMPLATE_DIR : ''), false, false);
	}

	/**
	 * compile template for other purpose
	 * @param string $other			template filename
	 * @param bool	 $use_layout	(optional d:false) indicate use layout or not
	 * @return string return compiled template
	 */
	public final function fetchOtherTemplate($other, $use_layout = false)
	{
		$content = $this->getTemplatePath() . self::OTHER_TEMPLATE_DIR . DIRECTORY_SEPARATOR . $other.'.tpl';
		return $this->_print($content, ($use_layout ? self::OTHER_TEMPLATE_DIR : ''), false, false);
	}

	/**
	 * assign all view overloaded_data to smarty variables
	 */
	private function _assignParams()
	{
        $this->assign($this->overloaded_data, null, true);
        $this->assign('messages', $this->messages, true);
//        $this->_tsmarty->assign($this->overloaded_data, null, true);
	}

	/**
	 * add to view params common variables, i.e. errors, messages, paging, menu.
	 */
	private function _setCommonParams()
	{
        if(!empty($this->_js_lang_vars))
            $this->js_lang_vars = $this->_js_lang_vars;
        if(!empty($this->_js_vars))
            $this->js_vars = $this->_js_vars;

		$this->_setMenuTemplate();
		$this->_setErrorTemplate();
		$this->_setMessageTemplate();
		$this->_setPagingTemplate();
	}

    /**
     * Add new error text for existing
     * @param string $error error text
     * @param string $key error array key
     * @param bool $to_session save to session
     */
    public final function addError($error, $key=null, $to_session=false)
    {
        if(is_null($key))
        {
            if($to_session)
                $_SESSION[self::SESS_NAME]['messages']['error']['general'][] = $error;
            else
                $this->messages['error']['general'][] = $error;
        }
        else
            if($to_session)
                $_SESSION[self::SESS_NAME]['messages']['error']['general'][$key] = $error;
            else
                $this->messages['error']['general'][$key] = $error;
    }
    
    /**
     * Add new error text for existing
     * @param string $error error text
     * @param string $key error array key
     */
    public final function addErrorToSession($error, $key=null)
    {
        $this->addError($error, $key, true);
    }

    /**
     * Add new form error for existing
     * @param string $element field name
     * @param string $error error text
     */
    public final function addFormError($element, $error, $to_session=false)
    {
        if (!$to_session && !isset($this->messages['error']['form'][$element]))
        {
            $this->messages['error']['form'][$element] = $error;
        }
        if($to_session)
            $_SESSION[self::SESS_NAME]['messages']['error']['form'][$element] = $error;
    }
    
    public final function addFormErrorToSession($element, $error)
    {
        $this->addFormError($element, $error, true);
    }

    /**
     * Add new success message
     * @param string $message message text
     */
    public final function addSuccess($message, $to_session=false)
    {
        if($to_session)
            $_SESSION[self::SESS_NAME]['messages']['success'][] = $message;
        else
            $this->messages['success'][] = $message;
    }
    
    public final function addSuccessToSession($message)
    {
        $this->addSuccess($message, true);
    }
    
    /**
     * Add new notice message
     * @param string $message message text
     */
    public final function addNotice($message, $to_session=false)
    {
        if($to_session)
            $_SESSION[self::SESS_NAME]['messages']['notice'][] = $message;
        else
            $this->messages['notice'][] = $message;
    }
    
    public final function addNoticeToSession($message)
    {
        $this->addNotice($message, true);
    }
    
    /**
     * Return user notice messages
     * @return array
     */
    public final function getNotice()
    {
        return $this->messages['notice'];
    }
    /**
     * Return user success messages
     * @return array
     */
    public final function getSuccess()
    {
        return $this->messages['success'];
    }
    /**
     * Return user form errors
     * @return array
     */
    public final function getFormErrors()
    {
        return $this->messages['error']['form'];
    }
    /**
     * Return user errors
     * @return array
     */
    public final function getErrors()
    {
        return $this->messages['error'];
    }

    /**
     * Add new lang variable to array to send to browser. Format key=>value
     * @param array $lang_var
     */
    public final function addJsLangVar(array $lang_var)
    {
        $this->_js_lang_vars = array_merge($this->_js_lang_vars, $lang_var);
    }

    /**
     * Add new variable to array to send to browser. Format key=>value
     * @param array $lang_var
     */
    public final function addJsVar(array $lang_var, $to_session=false)
    {
        if($to_session)
        {
            if(empty($_SESSION['js_vars'])) $_SESSION['js_vars']=array();
            $_SESSION['js_vars'] = array_merge($_SESSION['js_vars'], $lang_var);
        }
        else
        {
            $this->_js_vars = array_merge($this->_js_vars, $lang_var);
        }
    }
    /**
     * Add new variable to array to send to browser on next page load. Format key=>value
     * @param array $lang_var
     */
    public final function addJsVarToSession(array $lang_var)
    {
        $this->addJsVar($lang_var, true);
    }
    
    /**
     * Retutn js variable set to view
     * @param string|int $key array key
     * @return array if set $key param return value of that key, if not - return all js vars
     */
    public final function getJsVar($key='')
    {
        if(empty($key))
        {
            return $this->_js_vars;
        }
        else
        {
            return isset($this->_js_vars[$key]) ? $this->_js_vars[$key] : null;
        }
    }

	/**
	 * Add data to JSON array
	 * @param array $json_value
	 */
	public final function addToJson($json_value)
	{
		if (!empty($json_value))
			$this->_json = array_merge($this->_json, $json_value);
	}

	/**
	 * Send JSON to browser
	 * @param array $json_value
	 */
	public final function sendJson($json_value = null)
	{
		header('Content-type: text/javascript');
		if (!empty($json_value))
			$this->addToJSON($json_value);
		die (json_encode($this->_json));
	}
    
    /**
     * Set use or not section layout
     * @param bool $use (optional d:true)
     */
    public final function useSectionLayout($use=true)
    {
        $this->use_section_layout = $use;
    }
    
    /**
     * append smarty variable
     * @param string $to tpl variable
     * @param mixed $value 
     * @param bool $merge flag to merge or not
     */
//    public final function append($to, $value, $merge=true)
//    {
//        if(isset($this->$to))
//        {
//            if($merge && is_array($value))
//            {
//                foreach($value as $m_key => $m_val)
//                {
//                    $this->{$to}[$m_key] = $m_val;
//                }
//            }
//            else
//            {
//                $this->{$to}[] = $value;
//            }
//        }
//        else
//        {
//            $this->$to = $value;
//        }
////        $this->_tsmarty->append($to, $value, true);
//    }
}

/* End of file View.php */
/* Location: ./class/Base/View.php */
?>