<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AdminLikePagesController', false);
Loader::loadExtension('swfupload.SwfUpload');

/**
 * class CvController
 *
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class CvController extends AdminLikePagesController
{
    protected function _init()
    {
        parent::_init();
        
        $this->setSelectedMainMenu('cv');
        $this->setSelectedLeftMenu('cv');
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
                'href'  => $this->base_address . '/download',
                'params'=> array($this->primary_key)
            )
        );
        
        $this->addContentActionButton(
            array(
                'class' => 'preview',
                'title' => $this->_lang->button()->preview,
                'href'  => $edit_url,
                'params'=> array($this->primary_key),
                'condition' => array('key' => 'type', 'value' => 'online')
            )
        );
        $this->addContentActionButton(
            array(
                'class' => 'edit',
                'title' => $this->_lang->button()->edit,
                'href'  => $edit_url,
                'params'=> array($this->primary_key),
                'condition' => array('key' => 'type', 'value' => 'online')
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
            'href'  => $this->base_address . '/downloadtemplate',
            'js'    => 'download_template',
        ));
        
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->cv()->button_upload_cv,
            'href'  => $this->base_address,
            'js'    => 'upload_cv',
        ));
        
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->cv()->button_create_online,
            'href'  => $this->base_address . '/manage'
        ));
    }
    
    public function indexAction()
    {
        parent::indexAction();
        
        $this->_view->templates = $this->getTemplates('cv');
        
        Loader::loadExtension('Media');
        SwfUpload::init();
        SwfUpload::uploadSettings('cv', array(
            'upload_url' => App::controller()->domain_url . '/swfupload/uploadCv',
            'button_action'=>  SwfUpload::$swfUpload['button_action']['select_file'],
        ));
    }
    public function uploadAction()
    {
        SwfUpload::init();

        if($this->_request->isPost())
        {
            // fake id for test upload.
            $id = 15;
            
            // move uploaded single file
            SwfUpload::moveTmpFiles(Media::IMAGE_DIR, $this->img_dir.'cv'.DS, $id, 'redirect', 'cv');
            
            $this->redirect('cv');
        }
        
        SwfUpload::uploadSettings('image', array(
            'button_action'=>  SwfUpload::$swfUpload['button_action']['select_file'],
            'post_params'   => array(
                'size_limit' => serialize(array(
                    'photo' => array(
                        'min' => array(
                            'w' => 100,
                            'h' => 100
                        ),
                        'max' => array(
                            'w' => 1001,
                            'h' => 1002
                        ),
                    )
                ))
            ),
        ));
    }
    
    public function manageAction()
    {
        Loader::loadExtension('Media');
        
        parent::manageAction();
        
        SwfUpload::init();
        SwfUpload::uploadSettings('image', array(
            'button_action'=>  SwfUpload::$swfUpload['button_action']['select_file'],
            'post_params'   => array(
                'size_limit' => serialize(array(
                    'photo' => array(
                        'min' => array(
                            'w' => 100,
                            'h' => 100
                        ),
                        'max' => array(
                            'w' => 1001,
                            'h' => 1002
                        ),
                    )
                ))
            ),
        ));
        
        $this->prepareManage();
    }
    
    private function prepareManage()
    {
        $this->setGuideTips('cv');
        
        $this->_view->info = $this->createSelectOptions();
        $this->_view->templates = $this->getTemplates('cv');
    }
    
    protected function add()
    {
        parent::add();
        $this->_view->addJsVar(array('cv_nums' => array(
            'education' => 0,
            'work' => 0,
            'candc' => 0,
            'language' => 0,
        )));
        
        if(empty($this->_view->data['personal']))
        {
            $info = $this->_user->getUserInfo();
            $this->_view->data['personal'] = $info;
            $this->_view->data['personal']['address'] = array(
                'country_id' => '',
                'country' => $info['country'],
                'city' => $info['city'],
                'address' => $info['address'],
                'zip' => $info['zip'],
            );
        }
    }
    
    protected function edit()
    {
        parent::edit();
        $this->_view->addJsVar(array('cv_nums' => array(
            'education' => count($this->_view->data['education']),
            'work' => count($this->_view->data['work']),
            'candc' => count($this->_view->data['candc']),
            'language' => count($this->_view->data['language']),
        )));
    }
    
    protected function setTitleToBreadcrumb($id, $href=null, $field='title')
    {
        parent::setTitleToBreadcrumb($id, $href, 'name');
    }
    
    public function deleteItemAction()
    {
        $item = $this->_request->getPost('item', null, Request::FILTER_STRING);
        $item_id = $this->_request->getPost('item_id', null, Request::FILTER_INT);

        if($item && $item_id)
        {
            if($this->model($item,false)->delete(array('id'=>$item_id)))
            {
                $this->ajax->send(Ajax::RESULT_OK);
            }
        }
        $this->ajax->send(Ajax::RESULT_ERROR);
    }
    
    private function getTemplate($id)
    {
        return $this->model('Template')->getRow(array('id'=>$id));
    }

    private function outputTemplate($template)
    {
        Loader::loadLib('html2pdf/html2pdf.class');
        
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
//      $html2pdf->setModeDebug();
//        $html2pdf->setDefaultFont('Arial');
        $html2pdf->writeHTML('<page>'.$template['content'].'</page>');
        $html2pdf->Output($template['name'].'.pdf');
    }
    
    public function downloadTemplateAction()
    {
        $template = $this->getTemplate(1);
        
        $this->outputTemplate($template);
    }
    
    public function downloadAction()
    {
        $data = $this->model->getById($this->id);
        $template = $this->getTemplate($data['cv_info']['template_id']);
        if(empty($template))
        {
            $this->error404();
        }
        $this->outputTemplate($template);
    }
    
    public function uploadcvAction()
    {
        $this->model->saveUpload($this->data);
    }
}

/* End of file Cv.php */
/* Location: ./controllers/frontend/Cv.php */
?>