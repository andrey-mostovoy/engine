<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.13
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

require_once 'configs/RewriteUrl.php';

/**
 * class Request
 * processes url, post, files super global arrays
 *
 * @package		Base
 * @author		amostovoy
 */
class Request extends RewriteUrl
{
	/**
	 * admin side directory name
	 */
	const ADMIN_DIR = 'admin';
    /**
     * frontend side dir name
     */
    const FRONTEND_DIR = 'frontend';
    /**
     * Array with site parts such as admin or something else
     * in keys need to set url to that part of site and in its values directory name
     * @var array
     */
    private static $_site_parts = array();
	/**
	 * component class suffix
	 */
	const COMPONENT_SUFFIX = 'Component';
	/**
	 * controller class suffix
	 */
	const CONTROLLER_SUFFIX = 'Controller';
	/**
	 * action method suffix
	 */
	const ACTION_SUFFIX = 'Action';
    /**
	 * model class name prefix
	 */
	const MODEL_PREFIX = 'model';
    /**
     * method prefix of method called before call some action
     */
    const BEFORE_ACTION_PREFIX = 'before';
    /**
     * method prefix of method called after call some action
     */
    const AFTER_ACTION_PREFIX = 'after';
	/**
	 * default controller class name
	 */
	const DEFAULT_CONTROLLER = 'index';
	/**
	 * default action method name
	 */
	const DEFAULT_ACTION = 'index';
	/**
	 * default error controller class name
	 */
	const DEFAULT_ERROR_CONTROLLER = 'Error';
	/**
	 * default error action method name
	 */
	const DEFAULT_ERROR_ACTION = 'error';
    /**
     * filter contant - boolean type
     */
    const FILTER_BOOL = 'bool';
    /**
     * filter contant - integer type
     */
    const FILTER_INT = 'int';
    /**
     * filter contant - float type
     */
    const FILTER_FLOAT = 'float';
    /**
     * filter contant - array type
     */
    const FILTER_ARRAY = 'array';
    /**
     * filter contant - object type
     */
    const FILTER_OBJ = 'obj';
    /**
     * filter contant - string type. Use htmlentities and trim and urldecode
     */
    const FILTER_STRING = 'string_encoded';
    /**
     * filter contant - string type. Use html_entity_decode and trim and urldecode
     */
    const FILTER_STRING_CLEAR = 'string_decoded';
    /**
     * filter contant - string type. Simple set string type
     */
    const FILTER_STR = 'str';
	/**
	 * array of parameters given in url
	 * in format key1/value1/key2/value2
	 * @var array
	 */
	private $_params = array();
	/**
	 * instance of class
	 * @var Request
	 * @static
	 */
	private static $_instance = null;
	/**
	 * current controller name
	 * @var string
	 */
	private $_controller_name = null;
	/**
	 * Current action name
	 * @var string
	 */
	private $_action_name = null;
	/**
	 * current request url
	 * @var string
	 */
	private $_request_uri = null;
	/**
	 * url with host name and subdomain name if exist
	 * @var string
	 */
	private $_base_url = null;
    /**
     * dir to site root
     * @var string
     */
    private $_base_dir = null;
	/**
	 * domain url
	 * @var string
	 */
	private $_domain_url = null;
    /**
	 * subdomain name if exist
	 * @var string
	 */
	private $_subdomain = null;
	/**
	 * sub directory name where controllers are
	 * for example components/admin/...
	 * or where will be templates saved in that case. for example views/admin/...
	 * @var string
	 */
	private static $_dir = '';
    /**
	 * current site part url
	 * for example _HOST_/admin/...
	 * @var string
	 */
    private static $_url = '';

	private function __construct()
	{
        $this->_setSubdomain();
        $this->_setBaseDir();
		if (strlen($_SERVER['REQUEST_URI']) > 1)
			$this->_request_uri = substr( str_replace($this->_subdomain, '', $_SERVER['REQUEST_URI']), 1);
		else
			$this->_request_uri = '';

        
        $this->_prepareSiteParts();
		$this->_parseURL();
		$this->_setDomainUrl();
		$this->_setBaseUrl();
	}

	/**
	 * disable clone class
	 */
	public function __clone()
	{
		throw new InternalException('Denied to clone object of class '.__CLASS__);
	}

    public function getRequestUri()
    {
        return $this->_request_uri;
    }
    
    private function _prepareSiteParts()
    {
        $admin_url = defined('Config::ADMIN_URL') ? Config::ADMIN_URL : self::ADMIN_DIR;
        $admin_parts = array();
        
        foreach(Defines::$admin_site_parts as $u=>$d)
        {
            $admin_parts[$admin_url.'/'.$u] = self::ADMIN_DIR . DS . $d;
        }
        
        self::$_site_parts = array_merge(
                self::$_site_parts,
                array(
                    ''  => self::FRONTEND_DIR,
                    $admin_url => self::ADMIN_DIR
                ),
                Defines::$site_parts,
                $admin_parts
        );
    }

	/**
	 * Return singl instance of Request class
	 * @return Request
	 * @static
	 */
	public static function getInstance()
	{
		self::$_instance === null and self::$_instance = new self;
		return self::$_instance;
	}

	/**
	 * method separate from url controller name,
	 * action name and params formated key/val
	 */
	private function _parseURL()
	{
		if ( ($pos = strpos($this->_request_uri, '?')) !== false )
			$this->_request_uri = substr($this->_request_uri, 0, $pos);

		if ( empty($this->_request_uri) )
        {
            $this->setSitePart('');
			return;
        }

        if(!empty(self::$rewrite_urls))
        {
            foreach(self::$rewrite_urls as $url => $r_url)
            {
                if(strpos($this->_request_uri, $url) !== false)
                {
                    $this->_request_uri = str_replace(
                            $url, 
                            (isset($r_url['part']) ? $r_url['part'] . '/' : '')
                            . $r_url['controller']
                            . (isset($r_url['action']) ? '/' . $r_url['action'] : ''),
                            $this->_request_uri
                    );
                    break;
                }
            }
        }

		$url_patt = explode("/", $this->_request_uri);

        if($this->parseSection($url_patt))
            if($this->parseController($url_patt))
                if($this->parseAction($url_patt))
                    $this->parseParams($url_patt);
	}

    private function parseSection(&$url_patt)
    {
        $url = $part = self::getUrl();
        while(!empty($url_patt[0]) && array_key_exists($url_patt[0], self::$_site_parts))
        { // if we find in url name of special directory form site part
            $url = $part . array_shift($url_patt);
            if(isset(self::$_site_parts[$url]))
            {
                $part = self::$_site_parts[$url].'/';
            }
            else
            {
                array_unshift($url_patt, str_replace($part,'',$url));
                break;
            }
        }

        if(isset(self::$_site_parts[$url]))
        {
            $this->setSitePart($url);
        }
        else
        {
            $this->setSitePart(rtrim($part, '/'));
        }
        return true;
    }

    private function parseController(&$url_patt)
    {
//			if(preg_match('/[^a-z0-9а-я_\+\-]/i', $url_patt[$i]))
//			{
//				throw new RequestException('Wrong controller name');
//			}
        if(!empty($url_patt[0]))
        {
            return $this->setControllerName( array_shift($url_patt) );
        }
        return false;
    }

    private function parseAction(&$url_patt)
    {
        if(!empty($url_patt[0]))
        {
            if($url_patt[0] == '__a')
                return $this->setActionName( self::DEFAULT_ACTION );
            return $this->setActionName( array_shift($url_patt) );
        }
        return false;
    }

    public final function parseParams(&$url_patt)
    {
        for($i=0; $i<count($url_patt); $i++)
        {
            $this->_params[$url_patt[$i++]] = isset($url_patt[$i]) ? $url_patt[$i] : null;
        }
    }

	/**
	 * return subdirectory name of site part if exist
	 * @return string by default return empty string
	 */
	public final static function getDir()
	{
		return self::$_dir;
	}

    /**
	 * return url of site part if exist
	 * @return string by default return empty string
	 */
    public final static function getUrl()
    {
        return self::$_url;
    }

    /**
     * Get set of urls
     * @return array
     */
    public final function getRequestUrlParts()
    {
        $parts = explode('/', $this->_request_uri);
        $url = $this->getDomainUrl();
        foreach($parts as &$part)
        {
            $url = $part = $url .'/'. $part;
        }
        return $parts;
    }
	/**
	 * set subdirectory name and url for site part
	 * @param string $url url of site part
	 */
	public final function setSitePart($url=null)
	{
        if(!is_null($url))
        {
            self::$_url = $url;
            self::$_dir = self::$_site_parts[$url] . (!empty(self::$_site_parts[$url]) ? DIRECTORY_SEPARATOR : '');
        }
        else
        {
            self::$_url = '';
            self::$_dir = '';
        } 
        return true;
	}

	/**
	 * set current controller class name
	 * @param string $name
	 */
	public final function setControllerName($name)
	{
		return $this->_controller_name = strtolower($name); 
	}

	/**
	 * Return current controller class name
	 * @return string current controller name or by default
	 */
	public final function getControllerName()
	{
		return (!empty($this->_controller_name) ? $this->_controller_name : self::DEFAULT_CONTROLLER);
	}

	/**
	 * Set current action method name
	 * @param string $name
	 */
	public final function setActionName($name) { return $this->_action_name = strtolower($name); }

	/**
	 * Return current action method name
	 * @return string current action name or by default
	 */
	public final function getActionName()
	{
		return (!empty($this->_action_name) ? $this->_action_name : self::DEFAULT_ACTION);
	}

	/**
	 * Check request method
	 * @return boolean if request method is post return true, false otherwise
	 */
	public final function isPost() { return (strtoupper($_SERVER['REQUEST_METHOD'])=='POST'); }

    /**
     * Check if request send by ajax
     * @return bool
     */
    public final function isAjax()
    {
        return $this->getParam(
                '__a',
                $this->getPost(
                        '__a',
                        isset($_GET['__a']) ? true : false,
                        self::FILTER_BOOL),
                self::FILTER_BOOL);
    }

	/**
	 * Check uploaded file for upload errors
	 * and for right request extension
	 *
	 * @param string $filename key name in $_FILES array
	 * @param array $extention [optional] pass to check with this extensions.
	 *							requiered mime type (image/jpg for example)
	 * @return mixed return array with standart upload file information on success,
	 * in other cases return error code or false
	 */
	public function getFile($filename, $extention=null)
	{
		if(isset($_FILES[$filename]))
		{
			$file=$_FILES[$filename];
			$is_error = true;

			if ( $file['error'] == UPLOAD_ERR_OK)
			{
				$is_error = false;
			}

			if ( !$is_error )
			{
				if ( !empty($extention) )
				{ 
					$extention = (array) $extention;
					if ( !in_array($file['type'], $extention) )
						return UPLOAD_ERR_EXTENSION;// 'error_ext';
				}

				return $file;
			}
			else
			{
				return $file['error'];
			}
		}
		return false;
	}

	private function _getFileUploadError(&$r_file)
	{
		switch ($r_file['error'])
		{
			case UPLOAD_ERR_INI_SIZE:
                trigger_error('The uploaded file exceeds the upload_max_filesize directive in php.ini');
			break;
			case UPLOAD_ERR_FORM_SIZE:
                trigger_error('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
			break;
			case UPLOAD_ERR_PARTIAL:
                trigger_error('The uploaded file was only partially uploaded');
			break;
			case UPLOAD_ERR_NO_FILE:
                trigger_error('No file was uploaded');
			break;
			case UPLOAD_ERR_NO_TMP_DIR:
                trigger_error('Missing a temporary folder');
			break;
			case UPLOAD_ERR_CANT_WRITE:
                trigger_error('Failed to write file to disk');
			break;
			case UPLOAD_ERR_EXTENSION:
                trigger_error('File upload stopped by extension');
			break;
			case UPLOAD_ERR_OK:
//				$is_error = false;
			break;
			default:
                trigger_error('Unknown upload error');
		}
	}

	/**
	 * return value from REQUEST array
	 *
	 * @todo does it need ?
	 * @param string $param key of element in REQUEST array
	 * @param mixed $default default value of element if it is not exist
	 * @param mixed $filter type of return value
	 * @return mixed value of requred REQUEST array element
	 */
	public final function getRequest($param, $default=null, $filter=null)
	{
		return $this->_getElement($_REQUEST, $param, $default, $filter);
	}

	/**
	 * Return value from $_POST array
	 *
	 * @param string $param key of element in $_POST array
	 * @param mixed $default if no element return this value
	 * @param mixed $filter type of return value
	 *
	 * @return mixed value of requred POST array element after all requested transformations
	 */
	public final function getPost($param, $default=null, $filter=null)
	{
		return $this->_getElement($_POST, $param, $default, $filter);
	}
	/**
	 * Return value from $_GET array
	 *
	 * @param string $param key of element in $_GET array
	 * @param mixed $default if no element return this value
	 * @param mixed $filter type of return value
	 *
	 * @return mixed value of requred GEt array element after all requested transformations
	 */
	public final function getGet($param, $default=null, $filter=null)
	{
		return $this->_getElement($_GET, $param, $default, $filter);
	}

	/**
	 * Return value from _params array. That values passed in url in format key/value
	 * after controller and action names
	 *
	 * @param string $param key of element in _params array
	 * @param mixed $default if no element return this value
	 * @param mixed $filter type of return value
	 *
	 * @return mixed value of requred _params array element after all requested transformations
	 */
	public final function getParam($param, $default=null, $filter=null)
	{
		return $this->_getElement($this->_params, $param, $default, $filter);
	}

    /**
     * Retriev given params in string or array format
     * @param array $exclude exlude params
     * @param string $result_type return result format
     * @return string|array
     */
    public final function getQueryParams($exclude = null, $result_type = 'string')
    {
        $result = '';
        $exclude = (array) $exclude;
        foreach ($this->_params as $key => $value)
        {
            if (!in_array($key, $exclude))
            {
                if($result_type == 'array')
                {
                    $result[$key] = $value;
                }
                elseif($result_type == 'string')
                {
                    $result .= '/' . $key . '/' . $value;
                }
            }
        }
        return $result;
    }
    
	/**
	 * Return value from array.
	 *
	 * @param array $mas
	 * @param string $param key of element in array
	 * @param mixed $default if no element return this value
	 * @param mixed $filter type of return value
	 * @return mixed value of requred array element after all requested transformations
	 */
	private function _getElement( $mas, $param, $default = null, $filter = null )
	{
		if (!isset($mas[$param])) { return $default; }
		switch ($filter)
		{
			case null               : break;
			case self::FILTER_BOOL  : settype($mas[$param], 'boolean'); break;
			case self::FILTER_INT   : settype($mas[$param], 'integer'); break;
			case self::FILTER_FLOAT : settype($mas[$param], 'float'); break;
			case self::FILTER_ARRAY :
                settype($mas[$param], 'array');
                foreach($mas[$param] as $key => &$element)
                {
                    if(ctype_digit($element))
                    {
                        $a_filter = self::FILTER_FLOAT;
                    }
                    elseif(is_string($element))
                    {
                        $a_filter = self::FILTER_STRING;
                    }
                    elseif(is_array($element))
                    {
                        $a_filter = self::FILTER_ARRAY;
                    } 
                    $element = $this->_getElement($mas[$param], $key, $default, $a_filter);
                }
                break;
			case self::FILTER_OBJ   : settype($mas[$param], 'object'); break;
			case self::FILTER_STRING: return trim(htmlentities(urldecode($mas[$param]), ENT_QUOTES));
			case self::FILTER_STRING_CLEAR: return trim(html_entity_decode(urldecode($mas[$param]), ENT_QUOTES));
			case self::FILTER_STR   : settype($mas[$param], 'string'); break;
			default                 : throw new InternalException('Request error. Wrong filter.');
		}
		return $mas[$param];
	}

	/**
	 * set domain url of project
	 */
	private function _setDomainUrl()
	{
		if ($this->_domain_url === null)
		{
			$protocol = explode('/', $_SERVER['SERVER_PROTOCOL']);
			$this->_domain_url = strtolower($protocol[0]) . '://' .
								trim($_SERVER['HTTP_HOST']) .
								$this->_subdomain;
		}
	}

    /**
	 * set subdomain of project
	 */
    private function _setSubdomain()
    {
        $this->_subdomain = substr(trim($_SERVER['SCRIPT_NAME']),0,-10);
    }

    /**
	 * Return subdomain of project
	 * @return string
	 */
	public final function getSubdomain()
	{
		return $this->_subdomain;
	}

	/**
	 * Return domain url of project
	 * @return string
	 */
	public final function getDomainUrl()
	{
		return $this->_domain_url;
	}

	/**
	 * set base url of project
	 */
	private function _setBaseUrl()
	{
		if ($this->_base_url === null)
		{
			$this->_base_url = $this->_domain_url . '/' . $this->getUrl();
		}
	}

    /**
     * Set base dir of project
     */
    private function _setBaseDir()
    {
        if ($this->_base_dir === null)
		{
            if('/' != DS) // omg! )
            {
                $_SERVER['DOCUMENT_ROOT'] = str_replace('/', DS, $_SERVER['DOCUMENT_ROOT']);
            }
			$this->_base_dir = rtrim($_SERVER['DOCUMENT_ROOT'] . $this->_subdomain, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		}
    }

	/**
	 * Return base url of project
	 * @return string
	 */
	public final function getBaseUrl()
	{
		return rtrim($this->_base_url, '/');
	}

    /**
     * Return base dir of site
     * @return string
     */
    public final function getBaseDir()
	{
		return $this->_base_dir;
	}
}

/* End of file Request.php */
/* Location: ./class/Base/Request.php */
?>
