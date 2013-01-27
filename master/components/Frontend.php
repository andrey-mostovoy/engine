<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Frontend Component
 * @filesource
 */

/**
 * class FrontendComponent
 * Frontend module. Common methods for frontend side of site
 * @package		Project
 * @subpackage	Component
 */
class FrontendComponent extends CommonController
{
    protected function _init()
    {
        $this->_view->addJsVar(array('is_backend' => false));
        
        if ($this->_user->isAuth())
        {
            
        }
        else
        {
        }
    }
    
    protected function setCurrentActiveUser()
    {
        $this->_user->setCurrentAuth();
    }
    
    protected function formSiteTitle($title, $is_revers = true)
    {
        parent::formSiteTitle($title, $is_revers);
    }
    
    protected function setDefaultBreadCrumb()
    {
    }
}
/* End of file Frontend.php */
/* Location: ./components/Frontend.php */
?>