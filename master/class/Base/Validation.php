<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * class Validation
 * 
 *
 * @package		Base
 */
class BaseValidation
{
    const REQUIRED          = 'empty';
    const WRONG             = 'wrong';
    const LONG              = 'long';
    const SHORT             = 'short';
    const NOT_MATCH         = 'mismatch';
    const LESS              = 'less';
    const MORE              = 'more';
    const MAX_WORD_COUNT    = 'maxwordcount';
    const MIN_WORD_COUNT    = 'minwordcount';
    const ONEFROM           = 'onefrom';
    const FUNCTION_CHECK    = 'function';
    const UNIQUE            = 'unique';
    const WYSIWYG           = 'wysiwyg';
    const STRING            = 'string';
    
    //separators in validation rules
    const S_FIELD    = ',';
    const S_PROPERTY = ':';
    const S_PARAMS   = '|';
    
    private static $instance = null;
    
    /**
     * Found errors
     */ 
    private static $_errors = array();
    
    private static $error_tree = array();
    
    private function __construct()
    {
        
    }
    
    public static function getInstance()
    {
        is_null(self::$instance) and self::$instance = new Validation();
        return self::$instance;
    }
    
    /**
     * Generic function for all rules
     * @param (mixed|!object) $data
     * @return (bool)  
     */ 
    public final function validate($data, $rules, $isFull = false)
    {
        //init steps
        self::$_errors = array();
        
        if (empty($data))
        {
            $this->addError('data', self::REQUIRED);
            return false;
        }
        
        if (empty($rules))
        {
            return true;
        }

        //perform validation
        $isCorrect = $this->processValidate($data, $rules, $isFull);
        
        return $isCorrect;
    }
    
    private final function processValidate($data, $rules, $isFull)
    {
        //perform validation
        $isCorrect = true;
        foreach ($rules as $rule => $fields)
        {
            if($rule == Model::EACH)
            {
                foreach($fields as $each_key => $each_val)
                {
                    if($each_key == Model::ITEM || isset($data[$each_key]))
                    {
                        if($each_key != Model::ITEM)
                        {
                            self::$error_tree[] = $each_key;
                            $loop_data = $data[$each_key];
                        }
                        else
                        {
                            $loop_data = $data;
                        }
                        foreach($loop_data as $data_each_key=>$data_each_val)
                        {
                            self::$error_tree[] = $data_each_key;
                            $check = $this->processValidate($data_each_val, $each_val, $isFull);
                            array_pop(self::$error_tree);
                            if (!$check)
                            {
                                if (!$isFull) return false;
                                $isCorrect = false;
                            }
                        }
                        array_pop(self::$error_tree);
                    }
                }
            }
            elseif(is_array($fields))
            {
                self::$error_tree[] = $rule;
                $check = $this->processValidate($data[$rule], $fields, $isFull);
                array_pop(self::$error_tree);
                if (!$check)
                {
                    if (!$isFull) return false;
                    $isCorrect = false;
                }
            }
            else
            {
                $method = 'check' . implode('', array_map('ucfirst', explode('_',$rule)));
                
                if (method_exists($this, $method))
                {
                    $check = $this->$method($data, $this->_getFields($fields));

                    if (!$check)
                    {
                        if (!$isFull) return false;

                        $isCorrect = false;
                    }
                }
            }
        }
        return $isCorrect;
    }
    
    /**
     * Check is required fields not empty
     * @example 'required' => 'id,name'
     * @param (array) $data
     * @param (array|string) $required
     * @return (bool)
     */ 
    public final function checkRequired($data, $required)
    {
        $isCorrect = true;
    
        foreach ($required as $field)
        {
            if (!isset($data[$field]))
            {
                $this->addError($field, self::REQUIRED, array('field'=>$field));
                $isCorrect = false;
            }
            elseif ($this->hasEmpty($data[$field]))
            {
                $this->addError($field, self::REQUIRED, array('field'=>$field));
                $isCorrect = false;
//                unset($data[$field]); // for what?
            }
        }
        
        return $isCorrect;
    }
    
    /**
     * Check is required fields set
     * @param array $data
     * @param array $required
     * @return bool
     */
    public final function checkIsset($data, $required)
    {
        $isCorrect = true;
    
        foreach ($required as $field)
        {
            if (!isset($data[$field]))
            {
                $this->addError($field, self::REQUIRED, array('field'=>$field));
                $isCorrect = false;
            }
        }
        
        return $isCorrect;
    }
    
    /**
     * Check is fields more then passed num
     * @param (array) $data
     * @param (array|string) $required
     * @return (bool)
     */
    public final function checkMore($data, $fields)
    {
        return $this->range($data, $fields, '>');
    }
    
    /**
     * Check is fields less then passed num
     * @param (array) $data
     * @param (array|string) $required
     * @return (bool)
     */
    public final function checkLess($data, $fields)
    {
        return $this->range($data, $fields, '<');
    }
    
    private function range($data, $fields, $sign)
    {
        $isCorrect = true;
        foreach ($fields as $field)
        {
            $buf = explode(':', $field);

            $field = $buf[0];
            $compare = $buf[1];

            if (empty($data[$field])) continue;
            
            if ($sign=='<')
            {
                if ($data[$field] >= $compare)
                {
                    $isCorrect = false;
                    $this->addError($field, self::MORE, array('field'=>$field));
                }
            }
            else
            {
                if ($data[$field] <= $compare)
                {
                    $isCorrect = false;
                    $this->addError($field, self::LESS, array('field'=>$field));
                }
            }
        }

        return $isCorrect;
    }

    /**
     * Check if required fields are emails
     * @example 'email'    => 'email_field',
     * @param (array) $data
     * @param (array|string) $fields
     * @return (bool)
     */
    public final function checkEmail($data, $fields)
    {
        $isCorrect = true;
        foreach ($fields as $field)
        {
            if (!isset($data[$field])) continue;
            
            if (!preg_match(Defines::EMAIL_PATTERN, $data[$field]))
            {
                $isCorrect = false;
                $this->addError($field, self::WRONG, array('field'=>$field));
            }
        }
        
        return $isCorrect;
    }
    
    /**
     * Check if fields are valid emails
     * @example 'length'   => 'email:4|35,password:5|16',
     * @param (array) $data
     * @param (array|string) $fields
     * @return (bool)
     */
    public final function checkLength($data, $fields)
    {
        $isCorrect = true;
        foreach ($fields as $field)
        {
            $buf = explode(':', $field);
            
            $field = $buf[0];
            $length = explode('|', $buf[1]);
            
            if (empty($data[$field])) continue;
            
            if (strlen($data[$field]) < $length[0])
            {
                $isCorrect = false;
                $this->addError($field, self::SHORT, array('field'=>$field, 'length'=>$length[0]));
            }
            elseif (isset($length[1]) && strlen($data[$field]) > $length[1])
            {
                $isCorrect = false;
                $this->addError($field, self::LONG, array('field'=>$field, 'length'=>$length[1]));
            }
        }
        
        return $isCorrect;
    }
    
    /**
     * Check if fields are match
     * @example 'match'    => 'password|confirm_password',
     * @param (array) $data
     * @param (array|string) $fields
     * @return (bool)
     */
    public final function checkMatch($data, $fields)
    {
        $isCorrect = true;
        foreach ($fields as $field)
        {
            $pair = explode('|', $field);
            if (empty($data[$pair[0]]) && empty($data[$pair[0]])) continue;
            
            if (empty($data[$pair[0]]) || empty($data[$pair[1]]))
            {
                $isCorrect = false;
                $this->addError($pair[0], self::NOT_MATCH, array('field1'=>$pair[0], 'field2'=>$pair[1]));
            }
            elseif ($data[$pair[0]] != $data[$pair[1]])
            {
                $isCorrect = false;
                $this->addError($pair[0], self::NOT_MATCH, array('field1'=>$pair[0], 'field2'=>$pair[1]));
            }
        }
        
        return $isCorrect;
    }
    
    /**
     * Checks if fields are digits
     * @param (array) $data
     * @param (array|string) $fields
     * @return (bool)
     */
    public final function checkDigits($data, $fields)
    {
        $isCorrect = true;
        foreach ($fields as $field)
        {
            if (!isset($data[$field])) continue;

            if (!ctype_digit((string)$data[$field]))
            {
                $this->addError($field, self::WRONG, array('field'=>$field));
                $isCorrect = false;
            }
        }
        
        return $isCorrect;
    }

    /**
     * Check if fields are float
     * @param (array) $data
     * @param (array|string) $fields
     * @return (bool)
     */
    public final function checkFloat($data, $fields)
    {
        $isCorrect = true;
        foreach ($fields as $field)
        {
            if (!isset($data[$field])) continue;

            if (!preg_match('/^\d+(\.\d+)?$/',$data[$field]))
            {
                $this->addError($field, self::WRONG, array('field'=>$field));
                $isCorrect = false;
            }
        }

        return $isCorrect;
    }
    
    /**
     * Check word count
     * @param (array) $data
     * @param (array|string) $fields
     * @return (bool)
     */
    public final function checkWordcount($data, $fields)
    {
        $isCorrect = true;
        foreach ($fields as $field)
        {
            $buf = explode(':', $field);
            
            $field = $buf[0];
            $length = explode('|', $buf[1]);
            
            if (empty($data[$field])) continue;
            
            if (str_word_count($data[$field]) < $length[0])
            {
                $isCorrect = false;
                $this->addError($field, self::MIN_WORD_COUNT, array('field'=>$field, 'length'=>$length[0]));
            }
            elseif (isset($length[1]) && str_word_count($data[$field]) > $length[1])
            {
                $isCorrect = false;
                $this->addError($field, self::MAX_WORD_COUNT, array('field'=>$field, 'length'=>$length[1]));
            }
        }
        return $isCorrect;
    }

        /**
     * Check is array has empty fields
     * @param (array|int) $data
     * @return (bool)
     */ 
    public final function hasEmpty($data)
    {
        if (!is_array($data))
        {
            return empty($data);
        }
       
        foreach ($data as $field)
        {
            $check = $this->hasEmpty($field);
            
            if ($check)
            {
                return true;
            }
        }
    }
    /**
     * Return founded errors. Errors array will be empty after output
     * @param (bool) $isFull
     * @return (array) false if no errors
     */ 
    public final function getErrors($isFull = false)
    {
        if (empty(self::$_errors))
        {
            return array();
        }
        
        $errors = self::$_errors;
        self::$_errors = array();
        
        if (!$isFull)
        {
            return $errors[0];
        }
        
        return $errors;
    }
    
    /**
     * Add one error with message
     * @param (string) $name
     * @param (string) $message
     * @param (array)  $params  array with additional parameters, like lenght less than ..(number)
     */ 
    public final function addError($name, $message, $params=array())
    {
        self::$_errors[] = array(
            'tree' => self::$error_tree,
            'field' => !empty(self::$error_tree) ? 
                '['.ltrim(implode('][', self::$error_tree),']') . ']['.$name.']' : '['.$name.']',
            'name' => $name,
            'message' => $message,
            'param'=>$params);
    }
    
    /**
     * Explode string
     */  
    private function _getFields($fields)
    {
        return array_map('trim', explode(self::S_FIELD, $fields));
    }
    
    /**
     * check is one from fields isset
     * @param array $data
     * @param array $fields
     * @return bool 
     */
    public final function checkOnefrom($data, $fields)
    {
        foreach($fields as $field)
        {
            if(key_exists($field, $data))
                return true;
        }
        $this->addError($fields, self::ONEFROM);
        return false;
    }
    
    /**
     * call model method to validate something.
     * Name of the function must be the follow:
     * validationCheck%MethodName%. Example: validationCheckUnique
     * 
     * @param array $data
     * @param array $fields array with values in format: 
     * 'field', 'field:method' or 'field:model|method'
     * @param string $func function name part
     * @param string $error_type type of error: unique or something else
     * @return bool
     */
    private final function _callModelFunction($data, $fields, $func, $error_type)
    {
        $is_correct = true;
        
        foreach($fields as $f)
        {
            $model = null;
            $buf = explode(':', $f);
            $field = $buf[0];
            if(isset($buf[1]))
            {
                $cred = explode('|', $buf[1]);
            }
            if(!empty($cred[1]))
            {
                $model = $cred[0];
                $method = 'validationCheck'.implode('', array_map('ucfirst',explode('_', $cred[1])));
            }
            elseif(!empty($cred[0]))
            {
                $method = 'validationCheck'.implode('', array_map('ucfirst',explode('_', $cred[0])));
            }
            else
            {
                $method = 'validationCheck'.implode('', array_map('ucfirst',explode('_', $func)));
            }

            if(is_null($model))
            {
                $check = !App::controller()->getCModel()->{$method}($data, $field, isset($data[$field])?$data[$field]:null);
            }
            else
            {
                $check = !App::model($model, false)->{$method}($data, $field, isset($data[$field])?$data[$field]:null);
            }

            if( !$check )
            {
                $is_correct = false;
                $this->addError($field, $error_type, array('field'=>$field));
            }
        }
        return $is_correct;
    }
    
    /**
     * Check for custom validation.
     * Method name format: validationCheck%MethodName%
     * @param array $data
     * @param array $fields array with values in format: 
     * 'field', 'field:method' or 'field:model|method'
     * @return bool
     */
    public final function checkFunction($data, $fields)
    {
        return $this->_callModelFunction($data, $fields, 'function', self::FUNCTION_CHECK);
    }
    
    /**
     * Check for unique field. Use model validation. Model function called by default:
     * validationCheckUnique. Can set another model and method.
     * Method name format: validationCheck%MethodName%
     * @param array $data
     * @param array $fields array with values in format: 
     * 'field', 'field:method' or 'field:model|method'
     * @return bool
     */
    public final function checkUnique($data, $fields)
    {
        return $this->_callModelFunction($data, $fields, 'unique', self::UNIQUE);
    }
    
    /**
     * Check for not empty wysiwyg field.
     * @param array $data
     * @param array $fields
     * @return boolean 
     */
    public final function checkWysiwyg($data, $fields)
    {
        $isCorrect = true;
    
        foreach ($fields as $field)
        {
            if(isset($data[$field]))
            {
                $str = html_entity_decode(strip_tags(htmlspecialchars_decode($data[$field])));
                $res = preg_match('/[\w\d]+/', $str);
                if($res == 0)
                {
                    $isCorrect = false;
                    $this->addError($field, self::WYSIWYG, array('field'=>$field));
                }
            }
        }
        return $isCorrect;
    }
    
    /**
     *
     * @param type $data
     * @param type $fields
     * @return boolean 
     */
    public final function checkString($data, $fields)
    {
        $isCorrect = true;
    
        foreach ($fields as $field)
        {
            if(isset($data[$field]))
            {
                if(!is_string($data[$field]) || is_numeric($data[$field]))
                {
                    $isCorrect = false;
                    $this->addError($field, self::STRING, array('field'=>$field));
                }
            }
        }
        return $isCorrect;
    }
}

/* End of file BaseValidation.php */
/* Location: ./class/Base/Validation.php */
?>