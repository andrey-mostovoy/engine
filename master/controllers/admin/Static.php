<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Admin Static Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AdminCommonController', false);

/**
 * class StaticController
 * Admin Static module
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class StaticController extends AdminCommonController
{
    protected function _init() {
        parent::_init();
        
        if($this->_request->getActionName() == 'manage'
            || $this->_request->getActionName() == 'delete'
        ) {
            $this->model->type = $this->_request->getParam('type', 'private', Request::FILTER_STRING);
        }
        else
        {
            $this->model->type = $this->_request->getActionName();
        }
        
        //saved session variable in method tabs()
        $parent_id = $this->_request->getParam(
                'parent_id',
                isset($_SESSION['static_parent_id'][$this->model->type])?
                    $_SESSION['static_parent_id'][$this->model->type]:0,
                Request::FILTER_INT);
        
        $this->model->parent_id = $parent_id;
    }
    /**
     * create toolbar buttons
     * Use factory to create different toolbars on tabs
     */
    protected function toolbarButtons()
    {
        $this->{'toolbarButtons'.ucfirst($this->model->type)}();
    }
    private function toolbarButtonsPublic()
    {
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->button()->create,
            'href'  => $this->base_address . '/manage/type/'.$this->model->type
        ));
        
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
    private function toolbarButtonsPrivate()
    {
        if(Defines::DEV || $this->model->parent_id != 0)
        {
            $this->addToolbarActionButton(array(
                'title' => $this->_lang->button()->create,
                'href'  => $this->base_address.'/manage/type/'.$this->model->type
            ));
        }
        if($this->model->parent_id != 0)
        {
            $this->addToolbarActionButton(array(
                'title' => $this->_lang->button()->back,
                'href'  => $this->base_address . '/'.$this->model->type.'/parent_id/0'
            ));
        }
    }
    private function toolbarButtonsGuide()
    {
        if(Defines::DEV)
        {
            $this->addToolbarActionButton(array(
                'title' => $this->_lang->button()->create,
                'href'  => $this->base_address.'/manage/type/'.$this->model->type
            ));
            $this->addToolbarActionButton(array(
                'title'     => $this->_lang->button()->delete_selected,
                'href'      => $this->delete_url.'/type/'.$this->model->type,
                'js'        => 'group_delete',
                'class'     => self::BTN_T_GROUP_CLASS.' hidden'
            ));
        }
        if($this->model->parent_id != 0)
        {
            $this->addToolbarActionButton(array(
                'title' => $this->_lang->button()->back,
                'href'  => $this->base_address . '/'.$this->model->type.'/parent_id/0'
            ));
        }
    }
    /**
     * Create table headers
     */
    protected function formTableHeader()
    {
        $this->{'formTableHeader'.ucfirst($this->model->type)}();
    }
    private function formTableHeaderPublic()
    {
        $this->addTableHeader(
            array(
//                $this->_lang->static()->id,
                $this->_lang->static()->title,
                $this->_lang->static()->url,
                $this->_lang->admin_table()->actions
            )
        );
    }
    private function formTableHeaderPrivate()
    {
        $header = array(
//            $this->_lang->static()->id,
            $this->_lang->static()->title
        );
        if($this->model->parent_id != 0)
        {
            $header[] = $this->_lang->static()->url;
        }
        $header[] = $this->_lang->admin_table()->actions;
        
        $this->addTableHeader($header);
    }
    private function formTableHeaderGuide()
    {
        $header = array(
//            $this->_lang->static()->id,
            $this->_lang->static()->title
        );
        if(Defines::DEV)
        {
            $header[] = $this->_lang->static()->url;
        }
        $header[] = $this->_lang->admin_table()->actions;
        
        $this->addTableHeader($header);
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
        $this->{'actionButtons'.ucfirst($this->model->type)}();
    }
    private function actionButtonsPublic()
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
                'href'  => $this->base_address.'/delete',
                'js'    => 'delete_item',
                'params'=> array($this->primary_key)
            )
        );
    }
    private function actionButtonsPrivate()
    {
        $edit_url = $this->base_address.'/manage';
        $this->_view->addUrl('edit_link', $edit_url);

        if($this->model->parent_id == 0)
        {
            $this->addContentActionButton(
                array(
                    'class' => 'edit',
                    'title' => $this->_lang->button()->pages,
                    'href'  => $this->base_address.'/private',
                    'params'=> array('parent_id'=>'id')
                )
            );
        }
        $this->addContentActionButton(
            array(
                'class' => 'edit',
                'title' => $this->_lang->button()->edit,
                'href'  => $edit_url,
                'params'=> array($this->primary_key)
            )
        );
        if(Defines::DEV || $this->model->parent_id != 0)
        {
            $this->addContentActionButton(
                array(
                    'class' => 'delete',
                    'title' => $this->_lang->button()->delete,
                    'href'  => $this->base_address.'/delete',
                    'js'    => 'delete_item',
                    'params'=> array($this->primary_key)
                )
            );
        }
    }
    private function actionButtonsGuide()
    {
        $edit_url = $this->base_address.'/manage/type/'.$this->model->type;
        $this->_view->addUrl('edit_link', $edit_url);

        if($this->model->parent_id == 0)
        {
            $this->addContentActionButton(
                array(
                    'class' => 'edit',
                    'title' => $this->_lang->button()->pages,
                    'href'  => $this->base_address.'/guide',
                    'params'=> array('parent_id'=>'id')
                )
            );
        }
        if(Defines::DEV || $this->model->parent_id != 0)
        {
            $this->addContentActionButton(
                array(
                    'class' => 'edit',
                    'title' => $this->_lang->button()->edit,
                    'href'  => $edit_url,
                    'params'=> array($this->primary_key)
                )
            );
        }
        if(Defines::DEV)
        {
            $this->addContentActionButton(
                array(
                    'class' => 'delete',
                    'title' => $this->_lang->button()->delete,
                    'href'  => $this->base_address.'/delete',
                    'js'    => 'delete_item',
                    'params'=> array($this->primary_key)
                )
            );
        }
    }
   
    /**
     * edit action
     */
    protected final function edit()
    {
        $data = $this->model->getRow( array('id' => $this->id) );
        // show 404page if no data was found
        if(!is_array($data))
        {
            $this->error404();
        }
        $data['content'] = html_entity_decode($data['content']);
        $this->_view->data += $data;
        
        $this->addBreadCrumb(array(
            'title' => $this->_lang->static()->{'tab_'.$data['type']},
            'href' => $this->base_address.'/'.$data['type']
        ));
    }
    public function indexAction()
    {
        
    }
    
    
    
    
    private function tabs()
    {
        if($this->_request->isAjax())
        {
            $this->getDataForTable();
            echo $this->fetchElementTemplate();
            die();
        }
        else
        {
            $this->addBreadCrumb(array(
                'title' => $this->_lang->static()->{'tab_'.$this->model->type},
                'href' => $this->base_address.$this->model->type
            ));
            $_SESSION['static_parent_id'][$this->model->type] = $this->model->parent_id;
        }
        $this->_view->setTemplate('index');
    }
    public function publicAction()
    {
        $this->model->paging()->setPagingName('p');
        $this->_view->paging_name = 'p';
        $this->tabs();
        $this->_view->addJsVar(array('active_type'=>0));
    }
    public function privateAction()
    {
        $this->model->paging()->setPagingName('pr');
        $this->_view->paging_name = 'pr';
        $this->tabs();
        $this->_view->addJsVar(array('active_type'=>1));
    }
    public function guideAction()
    {
        $this->model->paging()->setPagingName('g');
        $this->_view->paging_name = 'g';
        $this->tabs();
        $this->_view->addJsVar(array('active_type'=>2));
    }
    
    protected function add()
    {
        $this->addBreadCrumb(array(
            'title' => $this->_lang->static()->{'tab_'.$this->model->type},
            'href' => $this->base_address.'/'.$this->model->type
        ));
        $this->_view->data['type'] = $this->model->type;
        $this->_view->data['parent_id'] = $this->model->parent_id;
    }
    
    protected function delete($ids=null)
    {
        $this->model->delete(array('or'=>array('id in'=>$ids,'parent_id in'=>$ids)));
        return true;
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
}

/* End of file Static.php */
/* Location: ./controllers/Admin/Static.php */
?>