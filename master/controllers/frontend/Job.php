<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AdminLikePagesController', false);

/**
 * class JobController
 *
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class JobController extends AdminLikePagesController
{
    protected function _init()
    {
        parent::_init();
        
        $this->setSelectedMainMenu('job');
        $this->setSelectedLeftMenu('job');
    }
    
    protected function formTableHeader()
    {
        $this->addTableHeader(
            array(
//                $this->_lang->static()->id,
                $this->_lang->cv()->name => $this->initHeader('name', false, true),
                $this->_lang->cv()->date => $this->initHeader(
                                                    array(
                                                        'field'=>'create_date',
                                                        'filter'=>false,
                                                        'order'=>true), 
                                                    array(
                                                        'style' => 'width:70px;')
                ),
                $this->_lang->table()->actions
            )
        );
    }
    
    protected function actionButtons()
    {
        $edit_url = $this->base_address.'/manage';
        $this->_view->addUrl('edit_link', $edit_url);

        $this->addContentActionButton(
            array(
                'class' => 'download',
                'title' => $this->_lang->button()->download,
                'href'  => $edit_url,
                'params'=> array($this->primary_key)
            )
        );
        
        $this->addContentActionButton(
            array(
                'class' => 'preview',
                'title' => $this->_lang->button()->preview,
                'href'  => $edit_url,
                'params'=> array($this->primary_key)
            )
        );
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
    
    protected function toolbarButtons()
    {
        $this->addToolbarActionButton(array(
            'title'     => $this->_lang->button()->delete_selected,
            'href'      => $this->delete_url,
            'js'        => 'group_delete',
            'class'     => self::BTN_T_GROUP_CLASS.' hidden'
        ));
        
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->cv()->button_download_cv_template,
            'href'  => $this->base_address
        ));
        
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->cv()->button_upload_cv,
            'href'  => $this->base_address
        ));
        
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->cv()->button_create_online,
            'href'  => $this->base_address . '/manage'
        ));
    }
}

/* End of file Job.php */
/* Location: ./controllers/frontend/Job.php */
?>