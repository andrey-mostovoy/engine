<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Admin Index Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AdminCommonController', false);

/**
 * class IndexController
 * Admin Index module
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */

class IndexController extends AdminCommonController
{
    protected function setDefaultBreadCrumb()
    {
        $this->addBreadCrumb(array(
            'title' => $this->_lang->{$this->module_lang}()->header
        ));
    }
    
    protected function createControllerModel($model, $site_part=true)
    {
    }

    protected function formTableHeader(){}
    
    public function indexAction()
    {
        if ( !$this->_user->isAdmin() )
        {
            $next = $this->_request->getParam('next', false, Request::FILTER_STR);
            $this->redirect($this->base_url.'/auth/login'.($next?'/next/'.$next:''));
            die;
        }
        else
        {
            $this->redirect($this->base_url.($next?'/'.$next:'/dashboard'));
            die;
        }
    }
}

/* End of file Index.php */
/* Location: ./controllers/Admin/Index.php */
?>