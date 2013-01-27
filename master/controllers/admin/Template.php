<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Admin Template Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AdminCommonController', false);

/**
 * class TemplateController
 * Admin Template module
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class TemplateController extends AdminCommonController
{
    protected function _init() {
        parent::_init();
        
        if($this->_request->getActionName() == 'manage'
            || $this->_request->getActionName() == 'delete'
        ) {
            $this->model->type = $this->_request->getParam('type', 'letter', Request::FILTER_STRING);
        }
    }
    
    public function __call($name, $arguments)
    {
        parent::__call($name, $arguments);
        $name = str_replace(Request::ACTION_SUFFIX, '', $name);
        if(in_array($name, array('letter','cv')))
        {
            $this->model->type = $name;
            $this->model->paging()->setPagingName($name);
            $this->_view->paging_name = $name;
            $this->indexAction();
            $this->_view->setTemplate('index');
        }
    }
    /**
     * Create table headers
     */
    protected function formTableHeader()
    {
        $this->{'formTableHeader'.ucfirst($this->model->type)}();
    }
    private function formTableHeaderLetter()
    {
        $this->addTableHeader(
            array(
                $this->_lang->template()->letter_name,
                $this->_lang->admin_table()->actions
            )
        );
    }
    private function formTableHeaderCv()
    {
        $this->addTableHeader(
            array(
                $this->_lang->template()->name,
                $this->_lang->admin_table()->actions
            )
        );
    }
    private function formTableHeaderEmail()
    {
        $this->addTableHeader(
            array(
                $this->_lang->template()->name,
                $this->_lang->template()->subject,
                $this->_lang->template()->from,
                $this->_lang->admin_table()->actions
            )
        );
    }
    
    /**
     * create toolbar buttons
     * Use factory to create different toolbars on tabs
     */
    protected function toolbarButtons()
    {
        if($this->model->type != 'email' || Defines::DEV)
        {
            $this->addToolbarActionButton(array(
                'title' => $this->_lang->button()->create,
                'href'  => $this->base_address . '/manage/type/'.$this->model->type
            ));
        }
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->button()->edit,
            'href'  => $this->base_address . '/manage/type/'.$this->model->type.'/id/',
            'js'    => 'group_edit',
            'class' => self::BTN_T_SINGLE_CLASS.' hidden'
        ));
        $this->addToolbarActionButton(array(
            'title'     => $this->_lang->button()->delete_selected,
            'href'      => $this->delete_url.'/type/'.$this->model->type,
            'js'        => 'group_delete',
            'class'     => self::BTN_T_GROUP_CLASS.' hidden'
        ));
    }
    
    /**
     * generate actions button for table content.
     * Use factory to create different actions on tabs
     * Note: set number of action button like:
     * $this->_view->content_actions_count = 3;
     * But by default will be uses count of array.
     */
    protected function actionButtons()
    {
        $edit_url = $this->base_address.'/manage/type/'.$this->model->type;
        $this->_view->addUrl('edit_link', $edit_url);

        $this->addContentActionButton(
            array(
                'class' => 'edit',
                'title' => $this->_lang->button()->edit,
                'href'  => $edit_url,
                'params'=> array($this->primary_key)
            )
        );
        $this->addContentActionButton(
            array(
                'class' => 'delete',
                'title' => $this->_lang->button()->delete,
                'href'  => $this->base_address.'/delete/type/'.$this->model->type,
                'js'    => 'delete_item',
                'params'=> array($this->primary_key)
            )
        );
    }
    
    protected function add()
    {
        $this->addBreadCrumb(array(
            'title' => $this->_lang->template()->{$this->model->type.'_header'},
            'href' => $this->base_address.'/'.$this->model->type
        ));
        $this->_view->data['type'] = $this->model->type;
    }
   
    /**
     * edit action
     */
    protected final function edit()
    {
        if($this->model->type == 'email')
        {
            $this->model->setNameForEmail();
        }
        $data = $this->model->getRow( array('id' => $this->id) );
        // show 404page if no data was found
        if(!is_array($data))
        {
            $this->error404();
        }
        $data['content'] = html_entity_decode($data['content']);
        
        if($this->model->type == 'email')
        {
            $data['type'] = $this->model->type;
        }
        
        $this->_view->data += $data;
        
        $this->addBreadCrumb(array(
            'title' => $this->_lang->template()->{$this->model->type.'_header'},
            'href' => $this->base_address.'/'.$this->model->type
        ));
    }
    
    protected function setTitleToBreadcrumb($id, $href=null, $field='name')
    {
        parent::setTitleToBreadcrumb($id, $href, $field);
    }
    
    public function emailAction()
    {
        $this->model->setNameForEmail();
        $this->model->type = 'email';
        $this->indexAction();
        $this->_view->setTemplate('index');
    }
    
    public function manageAction()
    {
        if($this->model->type == 'email')
        {
            $this->model->setNameForEmail();
        }
        parent::manageAction();
    }
    
    protected function save()
    {
        $res = false;
        if($this->validate())
            $res = $this->model->save($this->data(), $this->id);
        
        if($res)
        {
            $this->_view->addSuccessToSession($this->_lang->admin()->success_manage);
            $this->redirect($this->base_address.'/'.$this->model->type.'/'
                    .Controller::SAVE_FILTER.'/1/page/'
                    .$this->model->paging()->getBackPage());
        }
        return $res;
    }
    
    protected function delete($ids=null)
    {
        if($this->model->type == 'email')
        {
            $this->model->setNameForEmail();
        }
        return parent::delete($ids);
    }
}

/* End of file Template.php */
/* Location: ./controllers/admin/Template.php */
?>