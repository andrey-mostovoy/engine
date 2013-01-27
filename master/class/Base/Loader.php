<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.32
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * class Loader
 * class provides static methods to load engine classes, models, controllers and extended libs
 *
 * @package		Base
 * @author		amostovoy
 */
class Loader
{
	/**
	 * Already loaded classes, models.. etc
	 * @var array 
	 */
	private static $_loaded_files = array();
	/**
	 * Class directory path from project root
	 */
	const CLASS_DIR = 'class';
    /**
     * Extensions directory path
     */
    const EXTENSION_DIR = 'extension';
	/**
	 * Model directory path from project root
	 */
	const MODEL_DIR = 'models';
	/**
	 * Components directory path from project root
	 */
	const COMPONENT_DIR = 'components';
    /**
     * Controllers directory path from project root
     */
	const CONTROLLER_DIR = 'controllers';
	/**
	 * Libs directory path from project root
	 */
	const LIB_DIR = 'libs';
    /**
     * php file extension
     */
    const FILE_EXT = '.php';

	/**
	 * function include project classes
	 * @param mixed $classes array or string ',' separated of classes
	 * @static
	 */
	public static function loadClass($classes)
	{
		self::_formatData($classes); 
		foreach ($classes as $class)
		{ 
			if (!self::checkLoaded('c::'.$class))
			{
                $class = self::parsePath($class);
				if (strpos($class, '_') !== false)
					$class = self::CLASS_DIR.DIRECTORY_SEPARATOR.str_replace("_", DIRECTORY_SEPARATOR, $class).self::FILE_EXT;
				else
					$class = self::CLASS_DIR.DIRECTORY_SEPARATOR.$class.self::FILE_EXT;
				self::_checkFileExist($class);
				require_once $class;
			}
		} 
	}
    /**
     * function include project component extension
     * @param string|array $extensions array or string ',' separated of extensions
     */
    public static function loadExtension($extensions)
	{
		self::_formatData($extensions); 
		foreach ($extensions as $extension)
		{ 
			if (!self::checkLoaded('e::'.$extension))
			{
                $extension = self::parsePath($extension);
				
                $extension = self::CLASS_DIR.DIRECTORY_SEPARATOR
                            .self::EXTENSION_DIR.DIRECTORY_SEPARATOR
                            .$extension.self::FILE_EXT;
                
				self::_checkFileExist($extension);
				require_once $extension;
			}
		} 
	}

	/**
	 * function include project models
	 * @param mixed $models array or string ',' separated of models
     * @param bool  $site_part (optional) if it set to true file will be loaded from site part directory
	 * @static
	 */
	public static function loadModel($models, $site_part=true)
	{
		self::_formatData($models);
		foreach ($models as $model)
		{
			if (!self::checkLoaded('m::'.($site_part?Request::getDir():'').$model.'_model'))
			{
                $model = self::parsePath($model);
                if(strpos($model, '/') !== false)
                {
                    $model = explode('/', $model);
                    $model[count($model)-1] = ucfirst($model[count($model)-1]);
                    $model = implode('/', $model);
                }
                else
                {
                    $model = ucfirst($model);
                }
				$model = self::MODEL_DIR.DIRECTORY_SEPARATOR.($site_part?Request::getDir():'').$model.self::FILE_EXT;
				self::_checkFileExist($model);
				require_once $model;
			}
		}
	}

	/**
	 * function include project component
	 * @param mixed $controllers array or string ',' separated of components
	 * @static
	 */
	public static function loadComponent($components)
	{
		self::_formatData($components);		
		foreach ($components as $component)
		{
			if (!self::checkLoaded('cm::'.$component))
			{
                $component = self::parsePath($component);
                $component = self::COMPONENT_DIR . DIRECTORY_SEPARATOR
                                . $component
                                . self::FILE_EXT;
                
				self::_checkFileExist($component);
				require_once $component;
			}
		}
	}
    
	/**
	 * function include project controllers
	 * @param mixed $controllers array or string ',' separated of controllers
     * @param bool  $site_part (optional) if it set to true file will be loaded from site part directory
	 * @static
	 */
	public static function loadController($controllers, $site_part=true)
	{
		self::_formatData($controllers);		
		foreach ($controllers as $controller)
		{
			if (!self::checkLoaded('cn::'.($site_part?Request::getDir():'').$controller))
			{
                $controller = self::parsePath($controller);
                $tc = $controller;
                $controller = self::CONTROLLER_DIR . DIRECTORY_SEPARATOR
                                . ($site_part ? Request::getDir() : '')
                                . str_replace(Request::CONTROLLER_SUFFIX, '', $controller)
                                . self::FILE_EXT;
				if( !self::_checkFileExist($controller,$site_part ? false: true) )
                {
                    $controller = self::CONTROLLER_DIR . DIRECTORY_SEPARATOR
                                . str_replace(Request::CONTROLLER_SUFFIX, '', $tc)
                                . self::FILE_EXT;
                    
                    self::_checkFileExist($controller);
                }
				require_once $controller;
			}
		}
	}

	/**
	 * function include project libs
	 * @param mixed $libs array or string ',' separated of libs
	 * @static
	 */
	public static function loadLib($libs)
	{
		self::_formatData($libs);
		foreach ($libs as $lib)
		{
			if (!self::checkLoaded('l::'.$lib))
			{
				$lib = self::LIB_DIR.DIRECTORY_SEPARATOR.$lib.self::FILE_EXT;
				self::_checkFileExist($lib);
				require_once $lib;
			}
		}
	}

    /**
     * Check for already loaded file. If loaded - return true, false otherwise
     * @since 1.31
     * @param string $object filename to check
     * @return bool 
     * @static
     */
    private static function checkLoaded($object)
    {
        if (!in_array($object, self::$_loaded_files))
        {
            self::$_loaded_files[] = $object;
            return false;
        }
        return true;
    }

	/**
	 * function change input param if it is a string to array
	 * @param mixed $datas
	 * @static
	 */
	private static function _formatData(&$datas)
	{
		if ( is_string($datas) && !empty($datas) )
			$datas = explode(',', str_replace(' ', '', $datas));
	}

    private static function parsePath($name)
    {
        return str_replace('.', DIRECTORY_SEPARATOR, $name);
    }
    
	/**
     * function check file exist and if not found it generate IFileException
     * @param string $file file path
     * @param bool $throw (d:true) flag to throw exception
     * @return bool
     */
	public final static function _checkFileExist($file,$throw=true)
	{
		if (!file_exists($file))
        {
            if($throw)
                throw new FileNotFoundException('Can\'t find file <strong>'.$file.'</strong>');
            else
                return false;
        }
        return true;
	}
}

/* End of file Loader.php */
/* Location: ./class/Base/Loader.php */
?>