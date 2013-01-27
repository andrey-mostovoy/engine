<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.9
 * @since		Version 1.6
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * <p>class Lang</p>
 * <p>containing language specific text in class properties.
 * Basic usage:</p>
 * <p>
 * get some variable from user section
 * $this->_lang->user()->id;
 * </p>
 * <p>
 * get all user section
 * $this->_lang->user()->all();
 * </p>
 * <p>
 * get all language sections with they variables. setSection function need
 * if we already call lang before somewhere
 * $this->_lang->setSection(null)->all();
 * </p>
 * <p>
 * get error from general section
 * $this->_lang->general()->error()->t1;
 * $this->_lang->error()->general()->t2;
 * </p>
 * <p>
 * posibilities to replace some placeholders in variable
 * $this->_lang->general()->example;
 * $this->_lang->general()->params(array('var2'=>'test repl'))->example;
 * $this->_lang->general()->var2('test repl')->example;
 * Also placeholder like {:general:no_data} will search in section general key no_data
 * and paste its value if find
 * {@example} 'TEST SERACH VAR: {:general:no_data} or {var1} or {var2}'
 * </p>
 * <p>
 * That functionality also work on smarty. But object contains on $lang smarty variable
 * {@example} in smarty {$lang->dashboard()->site_name(Config::SITE_NAME)->welcome}
 * site_name isn't section on language file. In that case argument will place 
 * into _params array in site_name key for replace placeholder.
 * </p>
 * <p>
 * Also in smarty can use online translate functionality
 * @todo write translate save functionality and retrieve from file
 * {@example} {lang var1=Config::SITE_NAME}Translate {$lang->general()->home} ::var1 functionality{/lang}
 * {@see} SmartyPlugins::lang in base for more information
 * </p>
 * @package		Base
 * @subpackage  Lang
 * @author		amostovoy
 */
class Lang
{
	/**
	 * lang files directory
	 */
	const LANG_DIR = 'lang';
	/**
	 * default lang identity
	 */
	const DEFAULT_LANG = 'en';
	/**
	 * array key element name for errors messages
	 */
	const ERROR = '_errors';
    /**
	 * absolute lang dir path
	 * @var string
	 */
	private $_lang_dir = null;
    /**
     * translate commande for linux command line
     * @var string
     */
    private $_translate_cmd = null;
	/**
	 * current lang identity
	 * @var string
	 */
	private $_lang = null;
    /**
     * current section
     * @var string
     */
    private $_section = null;
	/**
	 * array with values of selected language
	 * @var array
	 */
	private $_lang_vars = array();
    /**
	 * indicate for retrieving error lang variable
	 * @var bool
	 */
	private $_is_error = false;
    /**
     * array with values to replace in variable
     * @var array
     */
    private $_params = array();
    /**
     * Lang object
     * @var Lang
     */
    private static $instance = null;

	/**
	 * class construct
	 */
	private function __construct()
	{
        $this->_lang_dir = App::request()->getBaseDir() . self::LANG_DIR . DIRECTORY_SEPARATOR;

        $this->_lang = App::user()->getLang();

        if ($this->_lang === null)
           $this->_lang = self::DEFAULT_LANG;
        else
            $this->_translate_cmd = '| translate-bin -f '.self::DEFAULT_LANG.' -t '.$this->_lang.' -s google';

       $this->loadLang();
	}

    /**
	 * Return instance of Lang class
	 * @return Lang
	 * @static
	 */
    public static function getInstance()
    {
        self::$instance === null and self::$instance = new self();
		return self::$instance;
    }
    /**
     * This function handle call a new section.
     * {@example} $this->_lang->user() in controller. user is a section.
     * Also for use in smarty templates purpose this call with argument,
     * that argument will place into _params array for replace placeholder
     * in retrieving variable
     * {@example} in smarty {$lang->dashboard()->site_name(Config::SITE_NAME)->welcome}
     * @param string $name called section name
     * @param mixed $arguments
     * @return \Lang|null 
     */
    public final function __call($name, $arguments)
    {
        // set params for replace in variable
        if(!empty($arguments))
        {
            $this->_params[$name] = $arguments[0];
            return $this;
        }
        // call section of language file
        if(isset($this->_lang_vars[$name]))
        {
            $this->_section = $name;
            return $this;
        }
        else
        {
            if($this->loadLangFile($name))
            {
                // call again __call method to set current section to $name
                return $this->$name();
            }
            trigger_error(__CLASS__.' section \''.$name.'\' not exist');
            return null;
        }
    }
    /**
	 * getter. return value of element
	 *
	 * @param string $key key value of element
	 * @return mixed
	 */
	public final function __get($key)
	{
        if($this->_is_error)
        {
            if(isset($this->_lang_vars[$this->_section][self::ERROR][$key]))
            {
                $this->_is_error = false;
                return $this->format($this->_lang_vars[$this->_section][self::ERROR][$key]);
            }
            else
            {
                trigger_error(__CLASS__.' error variable \''.$key.'\' in section \''.$this->_section.'\' not exist');
                return null;
            }
        }
        else
        {
            if(isset($this->_lang_vars[$this->_section][$key]))
            {
                return $this->format($this->_lang_vars[$this->_section][$key]);
            }
            else
            {
                trigger_error(__CLASS__.' variable \''.$key.'\' in section \''.$this->_section.'\' not exist');
                return null;
            }
        }
	}

    /**
     * check for present inaccessible properties
     * @param string $key
     * @return bool
     */
    public final function __isset($key)
    {
        if($this->_is_error)
        {
            return isset($this->_lang_vars[$this->_section][self::ERROR][$key]);
        }
        else
        {
            return isset($this->_lang_vars[$this->_section][$key]);
        }
    }
    
    public final function _isset()
    {
        if($this->_is_error)
        {
            return isset($this->_lang_vars[$this->_section][self::ERROR]);
        }
        else
        {
            return isset($this->_lang_vars[$this->_section]);
        }
    }
    
    /**
     * Return current lang abreviature
     * @return string
     */
    public final function getCurrentLang()
    {
        return $this->_lang;
    }
    /**
     * Set current section. Use it when need retrieve all lang vars.
     * In that case $name set to null
     * @param mixed $name
     * @return \Lang 
     */
    public final function setSection($name)
    {
        $this->_section = $name;
        return $this;
    }
	/**
	 * load language variables
	 * @param string $lang language identity or null if want lang by default
	 */
	public final function loadLang($lang=null)
	{
		if ($lang !== null)
			$this->_lang = $lang;

        $this->loadLangFile('common');
	}
    /**
     * load lang file and add to lang variables.
     * Replace internal placeholders
     * @param string $file
     * @return boolean true on success, flase otherwise
     */
    private function loadLangFile($file)
    {
        $file = strtolower($file);
        $lang_file = $this->_lang_dir.$this->_lang.DS.$file.Loader::FILE_EXT;
        if(File::checkExist($lang_file))
        {
            $inc = include_once $lang_file;
            $this->_lang_vars += $inc;
            if($this->internalReplace($inc))
            {
                foreach($inc as $k=>&$v)
                {
                    unset($this->_lang_vars[$k]);
                }
                $this->_lang_vars += $inc;
                $this->_section = null;
            }
            unset($inc);
            return true;
        }
        return false;
    }
    /**
     * replace internal placeholders in loaded new lang sections.
     * Placeholder format {:sectionname:varname} or if error section
     * {:sectionname:error:varname}
     * @staticvar int $num_repl number of founded placeholders
     * @param array $vars array of lang variables
     * @return int return number of replaces
     */
    private function internalReplace(&$vars)
    {
        static $num_repl=0;
        foreach($vars as &$v)
        {
            if(is_array($v))
            {
                $num_repl = $this->internalReplace($v);
            }
            elseif(false !== ($pos = strpos($v, '{:')))
            {
                $end = strpos($v,'}',$pos);
                $sub=substr($v,$pos+2,$end-2-$pos);
                $var = explode(':', $sub);
                if(isset($var[2]))
                    $var = $this->{$var[0]}()->{$var[1]}()->{$var[2]};
                else
                    $var = $this->{$var[0]}()->{$var[1]};
                $v = str_replace('{:'.$sub.'}',$var,$v);
                $num_repl++;
            }
        }
        unset($v);
        return $num_repl;
    }
    /**
     * set flag to use error variable
     * @return \Lang|null 
     */
    public function error()
    {
        if(isset($this->_lang_vars[$this->_section][self::ERROR]))
        {
            $this->_is_error = true;
            return $this;
        }
//        else
//        {
//            trigger_error(__CLASS__.' error section \''.self::ERROR.'\' in section \''.$this->_section.'\' not exist');
//            return null;
//        }
        return $this;
    }
    
    /**
     * retrieve all vars from current section or all lang vars
     * @return array 
     */
    public function all()
    {
        if($this->_is_error)
        {
            if(isset($this->_section) && isset($this->_lang_vars[$this->_section][self::ERROR]))
            {
                $this->_is_error = false;
                return $this->_lang_vars[$this->_section][self::ERROR];
            }
            else
            {
                trigger_error(__CLASS__.' error section \''.$this->_section.'\' not exist');
                return null;
            }
        }
        else
        {
            if(isset($this->_section) && isset($this->_lang_vars[$this->_section]))
            {
                return $this->_lang_vars[$this->_section];
            }
            else
            {
                return $this->_lang_vars;
            }
        }
    }
    /**
     * set params used for replace in retrived variable
     * @param array $params array with pairs key=>val. key in format: var.
     * But placeholder is {var}
     * @return \Lang 
     */
    public function params(array $params)
    {
        if(!empty($params))
            $this->_params = $params;
        return $this;
    }
    /**
     * add '{' and '}' to keys for replaced params.
     * {@uses} Lang format
     * @param string $v
     * @param mixed $k 
     */
    private function formatKeys(&$v, $k)
    {
        $v = '{'.$v.'}';
    }
    
    /**
     * replace in lang var placeholders like {var}.
	 * @param string $str lang variable
     * @return string 
     */
	private function format($str)
	{
        if(!empty($this->_params))
        {
            $keys = array_keys($this->_params);
            array_walk($keys, array($this,'formatKeys'));

            $str = self::replace($str, array_combine($keys, array_values($this->_params)));
            $this->_params=array();
        }
        return $str;
	}

	/**
	 * Return string with replaced placeholders
	 * @param string $r_str string where need replace text
	 * @param array $vars pairs search_text=>replace_text
	 */
	public static function replace($r_str, $vars)
	{
		if ( is_array( $vars ) )
		{
            return str_replace(
                    array_keys($vars),
                    array_values($vars),
                    $r_str
            );
  
		}
		else
		{
			trigger_error('No text formats. No array vars');
		}
	}
    
    public final function translate($text)
    {
        $shell_cmd = escapeshellcmd('echo ' . $text) . $this->_translate_cmd;
        $out = shell_exec($shell_cmd);

        return $out;
    }
}

/* End of file Lang.php */
/* Location: ./class/Base/Lang.php */
?>