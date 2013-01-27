<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Page Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('FrontendCommonController', false);

/**
 * class PageController
 * Page module
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class PageController extends FrontendCommonController
{
    /**
     * initializing controller
     */
	protected final function _init()
	{
        parent::_init();
	}

    /**
     * Do not create default site title
     */
    protected function setDefaultSiteTitle(){}
    protected function setDefaultBreadCrumb()
    {
        $this->addBreadCrumb(array(
            'title' => $this->_lang->general()->home,
            'href'  => $this->base_url
        ));
    }
    
    /**
     * works when somebody is calling to static page
     * @param type $name
     * @param type $arguments 
     */    
    public function  __call($name, $arguments)
    {
//        $alias = str_replace(Request::ACTION_SUFFIX, '', $name);
//        $page = explode('-', $alias);
        $this->formatPage();

        $this->_view->setTemplate('page');
    }
    public function showAction()
    {
        if(!$this->_user->isAuth())
        {
            $this->redirect();
        }
        $parent = $this->_request->getParam('id', null, Request::FILTER_INT);
        if(empty($parent))
        {
            $this->error404();
        }
        $child = $this->model->get(array('parent_id'=>$parent,'type'=>'private'),array(1));
        if(empty($child[0]))
        {
            $this->error404();
        }
        $this->getPage($child[0]['url']);
        $this->_view->setTemplate('page');
    }
    /**
     * takes page from DB
     */
    private function formatPage()
    {
        $alias = $this->_request->getActionName();
        $this->getPage($alias);
    }

    /**
     * takes page from DB
     * @param type $alias
     * @return type 
     */
    private function getPage($alias)
    {
        if(is_numeric($alias))
        { // take page by id
            $page = $this->model->getRow(array('id'=>$alias));
        }
        else
        { // take page by alias
            $page = $this->model->getRow(array('url'=>$alias));
        }

        if(!$page || $page['type'] == 'guide')
        {
            $this->error404();
        }
        
        if($page['type'] == 'private')
        {
            if(!$this->_user->isAuth())
            {
                $this->redirect();
            }
            $this->formRightMenu($page['parent_id']);
        }
        
        $page['content'] = html_entity_decode(preg_replace('/&nbsp;/m', ' ', $page['content']));
        
        // site title
        $this->formSiteTitle( $page['title'] );
        $this->addBreadCrumb(array(
            'title' => $page['title'],
        ));
        
        //section title
        $this->_view->section_header = $page['title'];
        
        //page meta
        $this->_view->meta = array(
            'keywords'      => $page['meta_keywords'],
            'description'   => $page['meta_description']
        );
        $this->_view->page = $page;
        $this->_view->active_page = $alias;
        return $page;
    }
    
    private function formRightMenu($parent)
    {
        $ptitle = $this->model->getOne('title',array('id'=>$parent));
        $this->addBreadCrumb(array(
            'title' => $ptitle,
            'href'  => $this->base_address.'/show/id/'.$parent
        ));
        $this->setSelectedMainMenu($parent);
        $menu = $this->model->get(array('parent_id'=>$parent), 'all');
        if(!empty($menu))
        {
            $this->_view->right_menu = $menu;
        }
    }
}

/* End of file Page.php */
/* Location: ./controllers/frontend/Page.php */
?>