<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Error Component
 * @author amostovoy
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * class ErrorComponent
 * process error responce
 * @package		Base
 * @subpackage	Component
 * @author		amostovoy
 */
class ErrorComponent extends CommonController
{
	protected function _init()
	{
        $this->_view->setTemplateSubDir($this->_request->getDir().Request::DEFAULT_ERROR_ACTION);
        
        if (Config::DEBUG)
		{
            $d = debug_backtrace();
			$this->_view->error = array(
                'message'	=> 'call function '.$d[4]['function'],
                'file'		=> $d[4]['file'],
                'line'		=> $d[4]['line'],
            );
		}
	}

    protected function setDefaultBreadCrumb()
    {
    }
    
    protected function setDefaultSiteTitle()
    {
    }
    
    protected function createControllerModel($model, $site_part=true)
    {
    }
    
    private function displayPage($page)
    {
        if (Config::DEBUG)
			$this->_view->setTemplate('errorDebug');
        else
        {
            $this->_view->setTemplate($page);
        }
    }
    
    public function errorAction($exc)
	{
        $this->error($exc);
	}
    
    public final function error($exc)
    {
		if (Config::DEBUG)
		{
			$this->_view->error = array('message'	=> $exc->getMessage(),
										'file'		=> $exc->getFile(),
										'line'		=> $exc->getLine()
										);
			$this->_view->excTrace = $exc->getTrace();
		}

        $this->error404();
    }
    
    public function error404Action()
    {
        $this->error404();
    }
    
    public final function error404()
    {
        $this->displayPage('error404');
    }
    
    public function errorAccessAction()
    {
        $this->errorAccess();
    }
    
    public final function errorAccess()
    {
        $this->displayPage('errorAccess');
    }
    
    public function backAction()
    {
        if(isset($_SESSION['redirect']['back']))
        {
            $url = $_SESSION['redirect']['back'];
            unset($_SESSION['redirect']['back']);
            $this->redirect($url);
        }
    }    
}

/* End of file Error.php */
/* Location: ./components/Error.php */
?>