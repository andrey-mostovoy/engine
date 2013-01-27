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

Loader::loadClass('Base.Controller.ControllerPermission');

/**
 * class ControllerValidation
 * 
 * main class for all controllers
 * containing basic methods and properties
 * for validation
 * 
 * @package     Base
 * @category    Controllers
 * @author      amostovoy
 * @abstract
 */
abstract class ControllerValidation extends ControllerPermission
{
    /**
     * Ajax validation module action. In child class must be implemented validate method
     * Responce content result 'ok' or 'error' and messages array
     */
    public function ajaxValidationAction()
    {
        if($this->_request->getPost('ajax_submit',false,  Request::FILTER_INT))
            $result = 'ok_ajax';
        else
            $result = 'ok';
        $messages = array();

        if(!$this->validate())
        {
            $this->collectValidateErrors(); 
            foreach($this->_view->getFormErrors() as $k => $v)
            {
                $messages['__data'.$k] = $v;
            }
            $result = 'error';
        }
        $tpl = '';
        if($result == 'error')
        {
            $tpl = $this->fetchElementTemplate('messages_summary');
        }
        $this->_view->sendJson( array('result'=>$result, 'messages'=>$messages, 'tpl'=>$tpl) );
    }
    /**
     * Collect to _view object form errors on validate process.
     */
    private function collectValidateErrors()
    {
        // collect errors from current model
        $this->addModelErrors();
    }
    /**
     * Validate data method. By default return true and generate warning
     * @return boll Return true if no validate error, false otherwise
     */
    protected function validate()
    {
        if(method_exists($this->model, 'validate'))
        {
            return $this->model->validate(
                $this->data(),
                $this->_request->getPost('validate_type', null, Request::FILTER_STRING));
        }
        trigger_error('No validate method implement in current module');
        return true;
    }
}
/* End of file ControllerValidation.php */
/* Location: ./class/Base/Controller/ControllerValidation.php */
?>