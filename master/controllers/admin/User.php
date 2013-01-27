<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Admin User Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AdminCommonController', false);

/**
 * class UserController
 * Admin User module
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class UserController extends AdminCommonController
{
	protected final function _init()
	{
        parent::_init();
    }
    
    /**
     * creates toolbar buttons
     */
    protected function toolbarButtons()
    {
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->button()->new,
            'href'  => $this->base_address . '/manage',
        ));
        
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->button()->edit,
            'href'  => $this->base_address . '/manage/id/',
            'js'    => 'group_edit',
            'class' => self::BTN_T_SINGLE_CLASS.' hidden'
        ));
        
        $this->addToolbarActionButton(array(
            'title'     => $this->_lang->button()->delete_selected,
            'href'      => $this->delete_url,
            'js'        => 'group_delete',
            'class'     => self::BTN_T_GROUP_CLASS.' hidden'
        ));
    }
    
    /**
     * Create table headers
     */
    protected function formTableHeader()
    {
        $this->addTableHeader(
            array(
                $this->_lang->user()->email => $this->initHeader('email', 'text', true),
                $this->_lang->user()->name => $this->initHeader('name', 'text', true),
                $this->_lang->user()->role => $this->initHeader('role',  
                        array(
                            User::ADMIN => $this->_lang->role()->admin,
                            User::ADMIN_FRONT => $this->_lang->role()->admin_front,
                            User::MEMBER => $this->_lang->role()->member,
                        ),
                        true),
                $this->_lang->user()->status => $this->initHeader('status', 
                        array(
                            1 => $this->_lang->status()->{User::$status[1]},
                            2 => $this->_lang->status()->{User::$status[2]},
                            3 => $this->_lang->status()->{User::$status[3]},
                            4 => $this->_lang->status()->{User::$status[4]},
                        ),
                        true),
                $this->_lang->user()->reg_date => $this->initHeader('reg_date', false, true),
                $this->_lang->admin_table()->actions => $this->initFilterButton()
            )
        );
    }
    
    protected function actionButtons()
    {
        $edit_url = $this->base_address.'/manage';
        $this->_view->addUrl('edit_link', $edit_url);

        $this->addContentActionButton(
            array(
                'class' => 'edit',
                'title' => $this->_lang->button()->edit,
                'href'  => $edit_url,
                'params'=> array($this->primary_key)
            )
        );
//        $this->addContentActionButton(
//            array(
//                'class' => 'view',
//                'title' => $this->_lang->button()->view,
//                'href'  => $edit_url,
//                'condition' => array(
//                    'key' => 'role',
//                    'value' => User::MEMBER
//                ),
//                'params'=> array($this->primary_key)
//            )
//        );
        $this->addContentActionButton(
            array(
                'class' => 'delete',
                'title' => $this->_lang->button()->delete,
                'href'  => $this->base_address.'/delete',
                'js'    => 'delete_item',
                'params'=> array($this->primary_key)
            )
        );
        
//        $this->_view->content_actions_count = 3;
    }

    protected function delete($ids=null)
    {
        foreach($ids as $k=>&$id)// check for admin self id in array to delete
        {
            if($this->_user->id == $id) // if find - redirect with error
            {
                $this->_view->addErrorToSession($this->_lang->admin()->error()->delete_myself);
                if($this->_request->isAjax())
                    $this->ajax->send(Ajax::RESULT_REDIRECT, $this->base_address);
                else
                    $this->redirect($this->base_address);
                exit();
            }
        }
        return parent::delete($ids);
    }
}

/* End of file User.php */
/* Location: ./controllers/Admin/User.php */
?>