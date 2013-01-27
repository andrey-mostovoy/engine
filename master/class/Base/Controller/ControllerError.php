<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.26
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass('Base.Controller.Controller');

/**
 * class ControllerError
 * 
 * main class for all controllers
 * containing basic methods and properties
 * for work with errors
 * @package     Base
 * @category    Controllers
 * @author      amostovoy
 * @abstract
 */
abstract class ControllerError extends Controller
{
    /**
     * Add model errors to stack 
     * @param (array) $errors
     * @param (string) $section in lang file
     * @param (bool) $isForm
     * @param bool $to_session (optional d:false) add this errors to session
     */ 
    public function addModelErrors($errors = null, $section = '', $isForm = true, $to_session=false)
    {
        if (!$errors)
        {
            $errors = $this->model->getErrors(true);

            if (!$errors) return;
        }

        if(empty($section))
        {
            $section = (isset($this->module_lang) ? $this->module_lang : $this->_request->getControllerName() );
        }
//        $section = $this->_getErrorSection($section);

        $errors = $this->_formErrorsId($errors);
        
        $this->_addErrors($section, $errors, $isForm, $to_session);
    }
    /**
     * Add model errors to stack to session
     * @param (array) $errors
     * @param (string) $section in lang file
     * @param (bool) $isForm
     */ 
    public function addModelErrorsToSession($errors = null, $section = '', $isForm = true)
    {
        $this->addModelErrors($errors, $section, $isForm, true);
    }
    
    /**
     * Add errors
     * @param (string|array) $errors
     * @param (string) $section in lang file
     * @param (bool) $isForm
     */ 
    public function addErrors($errors, $section = '', $isForm = false)
    {
        if (!$errors)
        {
           return;
        }
        elseif (!is_array($errors))
        {
            $errors = array(array('name' => '', 'message' => $errors, 'source' => 'controller'));
        }

        $section = $this->_getErrorSection($section);
        
        $this->_addErrors($section, $errors, $isForm);
    }
    
    /**
     * Form errors id
     * @param (array) $errors
     * @return (array)
     */ 
    private function _formErrorsId($errors)
    {
        foreach ($errors as &$error)
        {
            $error['message'] = $error['name'] . '_' . $error['message'];
        }
        
        return $errors;
    }
    
    /**
     * Find error section
     * @param (string) $section
     * @return (string)
     */ 
    private function _getErrorSection($section = '')
    {
        if (!$section)
        {
            $section = (isset($this->module_lang) ? $this->module_lang : $this->_request->getControllerName() );
        }
        if (!$this->_lang->{$section}()->error()->_isset())
        {
            $section = 'form';
        }
        
        if (!$this->_lang->{$section}()->error()->_isset())
        {
            $section = 'general';
        }
        return $section;
    }
    
    private function _getErrorSectionsFromStack($stack)
    {
        if($stack)
        {
            foreach($stack as $k => &$s)
            {
                if(is_numeric($s))
                    unset($stack[$k]);
            }
            unset($s);
        }
        return $stack;
    }
    /**
     * Add errors into form or common errors
     * @param (string) $section
     * @param (array) $errors
     * @param (bool) $isForm this errors for html form
     * @param bool $to_session (optional d:false) add this errors to session
     */ 
    private function _addErrors($section, $errors, $isForm, $to_session=false)
    {
        $lookup_sections = array(
            $section, 'form', 'general'
        );

        $errors = (array) $errors;
        foreach ($errors as &$error)
        {
            $possible_sections = $lookup_sections;
            if(false !== ($lpos = strrpos($error['message'], '[')))
            {
                $message_lang = substr($error['message'], strrpos($error['message'], '[') + 1);
                $message_lang = str_replace(array('[',']'), '', $message_lang);
            }
            else
            {
                $message_lang = $error['message'];
            }

            $stack_sections = $this->_getErrorSectionsFromStack($error['stack']);
            if($stack_sections)
            {
                foreach($stack_sections as &$ss)
                {
                    array_unshift($possible_sections, $ss);
                }
                unset($ss);
            }

            //try to find first message like 'email_wrong' or 'name_empty' 
            //in lang files
            $message = $this->getErrorMessage($possible_sections, $message_lang);
            
            if(!$message)
            {
                //try to find part of message in lang (e.g. in "email_wrong" look for "wrong")
                $message_lang_buf = substr($message_lang, strrpos($message_lang, '_') + 1);
                $message = $this->getErrorMessage($possible_sections, $message_lang_buf);
            }
            
            if(!$message)
            {
                trigger_error('Can\'t find error message for '
                            .$message_lang.' in sections '
                            .implode(',', $possible_sections));
            }
            elseif(!empty($error['param']))
            {
                $message = $this->replaceErrorPlaceholders(
                        $message,
                        $possible_sections,
                        $error['param']
                );
            }

            if ($isForm && 'validation' == $error['source']) //show in form only validation errors
            {
                if($to_session)
                    $this->_view->addFormErrorToSession($error['name'], $message);
                else
                    $this->_view->addFormError($error['name'], $message);
            }
            else
            {
                if($to_session)
                    $this->_view->addErrorToSession($message);
                else
                    $this->_view->addError($message);
            }
        }
    }
    
    private function getErrorMessage($possible_section, $lang_code)
    {
        return $this->getMessage($possible_section, $lang_code, true);
    }
    private function replaceErrorPlaceholders($message, $possible_section, $params)
    {
        foreach($params as $k=>&$ins_par)
        {
            if(strpos($k, 'field') !== false)
            {
                $replace = $this->getMessage($possible_section,$ins_par);
                if(!$replace)
                {
                    $replace = $ins_par;
                }
            }
            else
            {
                $replace = $ins_par;
            }
            $message = str_replace('{'.$k.'}', $replace, $message);
        }
        return $message;
    }
    private function getMessage($possible_section, $lang_code, $is_error=false)
    {
        if(is_array($possible_section))
        {
            foreach($possible_section as &$ps)
            {
                if(($res = $this->getMessage($ps, $lang_code, $is_error)))
                {
                    return $res;
                }
            }
        }
        else
        {
            $this->_lang->setSection($possible_section);
            if ($this->_lang->_isset()
                && (
                    ($is_error && isset($this->_lang->error()->$lang_code))
                    || (!$is_error && isset($this->_lang->$lang_code))
                )
            ) {
                return $is_error ? $this->_lang->error()->$lang_code : $this->_lang->$lang_code;
            }
            else
            {
                return false;
            }
        }
    }
    
    protected function errorAccess()
    {
        App::controller('Error')->errorAccess();
        $this->_end();
        exit();
    }
    
    protected function error404()
    {
        App::controller('Error')->error404();
        $this->_end();
        exit();
    }
}
/* End of file ControllerError.php */
/* Location: ./class/Base/Controller/ControllerError.php */
?>