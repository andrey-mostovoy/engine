<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Common
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass('Base_DB_BaseDb');

/**	
 * class ModelError.
 * Containing common methods and class properties for work with model errors.
 * 
 * @package		Base
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class ModelError extends BaseDb
{
    /**
     * Found errors
     * @var array
     */ 
    protected $_errors = array();
    
    /**
     * Add error to stack
     * @param (string) $name
     * @param (string) $message
     * @param array  $params (optional d:null)
     * @param string $source (optional d:'model') where error appeared
     * @param string $stack (optional d:null) stack of error (for validate)
     */ 
    protected function addError($name, $message, $params=null, $source='model', $stack=null)
    {
        $this->_errors[] = array(
            'name' => $name,
            'message' => $message,
            'param'=>$params,
            'source' => $source,
            'stack' => $stack,
        );
    }
    /**
     * Add errors from related models (during validation proccess)
     * to error stack of current model. See details of 
     * {@see ModelValidation::collectRelatedModelValidationErrors}
     * @param array $errors error array from other model
     */
    protected function addRelatedModelErrors($errors)
    {
        foreach ($errors as &$error)
        {
            array_push($this->_errors, $error);
        }
        unset($error);
        
    }
    /**
     * Add errors
     * @param (string) $name
     * @param (string) $message
     * @param (string) $source where error appeared
     */  
    protected function addErrors($errors, $source = 'model')
    {
        $errors = (array) $errors;
        
        foreach ($errors as &$error)
        {
//            unset($error['tree']);
            $this->addError($error['field'], $error['message'], $error['param'], $source, $error['tree']);
        }
        unset($error);
    }
    /**
     * Return founded errors. Errors array will be empty after output
     * @param (bool) $isFull
     * @return (array|bool) false if no errors
     */ 
    public function getErrors($isFull = false)
    {
        if (empty($this->_errors))
        {
            return false;
        }
         
        $errors = $this->_errors;
        $this->_errors = array();
        
        if (!$isFull)
        {
            return $errors;
        }

        return $errors;
    }
}

/* End of file ModelError.php */
/* Location: ./class/Common/DB/ModelError.php */
?>
