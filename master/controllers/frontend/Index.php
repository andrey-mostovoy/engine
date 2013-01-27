<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('FrontendCommonController', false);

/**
 * class IndexController
 *
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class IndexController extends FrontendCommonController
{
    protected final function _init()
    {
        parent::_init();
        
        if($this->_user->isAuth())
        {
            $this->redirect($this->base.'/page/show/id/3');
        }
        
//        if($this->_view->isErrors())
//        {
//            $this->_view->addJsVar($this->_view->getErrors());
//        }
    }

    protected function formSiteTitle($title, $is_revers = false)
    {
        parent::formSiteTitle($title, $is_revers);
    }
    
    protected function createControllerModel($model, $site_part=true)
    {
        parent::createControllerModel('User');
    }
    
    public function indexAction()
    {
    }
}

/* End of file Index.php */
/* Location: ./controllers/frontend/Index.php */
?>