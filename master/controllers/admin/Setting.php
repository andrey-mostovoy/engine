<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Admin Setting Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AdminCommonController', false);

/**
 * class SettingController
 * Admin Setting module
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class SettingController extends AdminCommonController
{
    /**
     * create toolbar buttons
     */
    protected function toolbarButtons()
    {
        if(Defines::DEV)
        {
            $this->addToolbarActionButton(array(
                'title' => $this->_lang->button()->create,
                'href'  => $this->base_address . '/manage'
            ));
        }
        
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
    
    protected function setTitleToBreadcrumb($id, $href = null, $field = 'name')
    {
        parent::setTitleToBreadcrumb($id, $href, $field);
    }
    
    /**
     * Create table headers
     */
    protected function formTableHeader()
    {
        $this->addTableHeader(
            array(
                $this->_lang->setting()->name => $this->initHeader('name', 'text', true),
                $this->_lang->setting()->value => $this->initHeader('value', 'text', true),
                $this->_lang->admin_table()->actions => $this->initFilterButton()
            )
        );
    }
}

/* End of file Setting.php */
/* Location: ./controllers/Admin/Setting.php */
?>