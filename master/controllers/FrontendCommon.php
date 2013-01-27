<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Frontend Common Controller
 * @filesource
 */

Loader::loadComponent('Frontend');

/**
 * class FrontendCommonController
 * FrontendCommon module. Common methods for frontend side of site
 * @package		Project
 * @subpackage	Controllers
 */
class FrontendCommonController extends FrontendComponent
{
    protected function _init()
    {
        parent::_init();
        
        if($this->_user->isAuth())
        {
            $this->initMainMenu();
            $this->initLeftMenu();
            $this->getUserInfo();
        }
    }
    
    protected function setSelectedMainMenu($selected)
    {
        switch($selected)
        {
            case 'cv':
                $selected = 6;
            break;
        }
        return parent::setSelectedMainMenu($selected);
    }
    
    private function initMainMenu()
    {
        $this->loadModel('page');
        $mmenu = $this->mod_page->getPrivateForMainMenu();
        
        $this->_view->mmenu = $mmenu;
    }
    
    private function initLeftMenu()
    {
        $this->_view->lmenu = array(
            'profile' => array(
                'href' => '',
                'title' => $this->_lang->toolbar()->profile,
            ),
            'cv' => array(
                'href' => 'cv',
                'title' => $this->_lang->toolbar()->cv,
            ),
            'letter' => array(
                'href' => 'letter',
                'title' => $this->_lang->toolbar()->letters,
            ),
            'application' => array(
                'href' => 'application',
                'title' => $this->_lang->toolbar()->applications,
            ),
            'job' => array(
                'href' => 'job',
                'title' => $this->_lang->toolbar()->job,
            ),
            'company' => array(
                'href' => 'company',
                'title' => $this->_lang->toolbar()->companies,
            ),
        );
    }
    
    protected function setSelectedLeftMenu($menu)
    {
        $this->_view->selected_left_menu = $menu;
    }
    
    protected function getUserInfo()
    {
        $arr = $this->model('user')->getUserInfo();
        if(!empty($arr))
            $this->user()->setUserInfo($arr);
    }
    
    protected function setGuideTips($section)
    {
        $this->_view->guide_tip = $this->model('page')->getGuide($section);
    }
    
    protected function getTemplates($type)
    {
        $this->model('template')->type = $type;
        
        return $this->model('template')->getList();
    }
}
/* End of file FrontendCommon.php */
/* Location: ./controllers/FrontendCommon.php */
?>