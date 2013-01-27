<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Admin Dashboard Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AdminCommonController', false);

/**
 * class DashboardController
 * Admin Dashboard module
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class DashboardController extends AdminCommonController
{
	protected final function _init()
	{
        parent::_init();
	}
    
    protected function formTableHeader(){}
    
    public function indexAction()
    {
        $this->_view->setTemplate('welcome');
    }
}

/* End of file Dashboard.php */
/* Location: ./controllers/Admin/Dashboard.php */
?>