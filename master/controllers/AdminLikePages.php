<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Admin Like Pages Controller
 * @filesource
 */

Loader::loadController('FrontendCommonController', false);

/**
 * class AdminLikePagesController
 * Frontend module. Common methods for frontend side of site for page
 * like admin side, i.e. with table as index and manage actions
 * @package		Project
 * @subpackage	Controllers
 */
abstract class AdminLikePagesController extends FrontendCommonController
{
    /**
     * tolbar buttons class names for single item
     * action
     */
    const BTN_T_SINGLE_CLASS = 'js_single_action';
    /**
     * tolbar buttons class names for group item
     * action
     */
    const BTN_T_GROUP_CLASS = 'js_group_action';
    
    /**
     * lang section for module
     * @var string
     */
    protected $module_lang = '';
    /**
     * primary key id, or meta_id or else
     * @var string
     */
    protected $primary_key = 'id';

    protected $savebutton = true;

    protected $backbutton = true;
    
    
    protected $delete_url;
    
    protected function _init()
    {
        if(empty($this->module_lang))
        {
            $this->module_lang = $this->_request->getControllerName();
        }
//        $this->formSiteTitle($this->_lang->admin()->site_title);
        
        $this->id = $this->_request->getPost($this->primary_key, $this->_request->getParam($this->primary_key, null, Request::FILTER_INT), Request::FILTER_INT);

//        if(isset($this->model))
//            $this->model->paging()->setChooseIpp( Config::$numpage_choose );

        $js_lang = $this->_lang->{$this->module_lang}()->all();
        foreach($js_lang as &$v)
        {
            $v = preg_replace('/[\n\r\t]+/', ' ', $v);
        }
        $this->_view->addJsLangVar($this->_lang->form()->error()->all());
        $this->_view->addJsLangVar($js_lang);

        $this->_view->primary_key = $this->primary_key;

        $this->_view->params =  $this->_request->getQueryParams(array("__a","__filter"));
      
        $this->delete_url = $this->base_address . '/delete';
        
        //correct filter
        $this->filtername = 'filter_' . $this->_request->getControllerName(); 
            
        parent::_init();
	}
    
    protected function formSiteTitle($title, $is_revers = false)
    {
        parent::formSiteTitle($title, $is_revers);
    }
    
    protected function setDefaultBreadCrumb()
    {
        $this->addBreadCrumb(array(
            'title' => $this->_lang->general()->home,
            'href'  => $this->base_url
        ));
        
        $this->addBreadCrumb(array(
            'title' => $this->_lang->{$this->module_lang}()->header
        ));
    }
    protected function setDefaultSiteTitle()
    {
        $this->formSiteTitle($this->_lang->{$this->module_lang}()->site_title);
    }

    /**
     * Get title from frontendmenu and place it to breadcrumb
     * @param int $id frontendmenu id
     * @param string $href
     * @param string $field (optional) (default:title) field name in db
     */
    protected function setTitleToBreadcrumb($id, $href=null, $field='title')
    {
        if(method_exists($this->model, 'getTitle'))
        {
            $title = $this->model->getTitle($id);
        }
        else
        {
            $title = $this->model->getOne($field, array('id'=>$id));
        }
        $b = array(
            'title' => $title
        );
        if(!is_null($href))
        {
            $b['href'] = $href;
        }
        $this->addBreadCrumb($b);
    }
    /**
     * Add action to toolbar panel
     * @param array $action can be following parameters
     *              - title
     *              - href
     *              - js - some javascript function name. It will be assign to onclick event
     *              - id - button id
     *              - class - button class
     */
    protected final function addToolbarActionButton(array $action)
    {
        if(!isset($this->_view->toolbar))
        {
            $this->_view->toolbar = array();
        }
        $this->_view->toolbar[] = $action;
    }
    /**
     * generate actions button for table content.
     * Note: set number of action button like:
     * $this->_view->content_actions_count = 3;
     * But by default will be uses count of array.
     */
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

    /**
     * Add action to each table result row
     * @param array $action can be following parameters
     *              - title
     *              - href
     *              - class
     *              - js - some javascript function name. It will be assign to onclick event
     *              - condition - if condition is true, than display option e.g. array('key' => keyInContent, 'value' => neededValue, 'set' => setValue)
     *              - params - array with query params e.g. id=>content_id, meta_id=>content_meta_id
     */
    protected final function addContentActionButton($action)
    {
        if(!isset($this->_view->content_actions))
        {
            $this->_view->content_actions = array();
        }
        $this->_view->content_actions[] = $action;
    }

    /**
     * Add header for table content
     * @param string|array $header
     */
    protected final function addTableHeader($header)
    {
        if(!isset($this->_view->table_headers))
        {
            $this->_view->table_headers = array();
        }
        if(is_array($header))
        {
            $this->_view->table_headers += $header;
        }
        else
        {
            $this->_view->table_headers[] = $header;
        }
    }

    /**
     * create toolbar buttons
     */
    protected function toolbarButtons()
    {
        $this->addToolbarActionButton(array(
            'title' => $this->_lang->button()->create,
            'href'  => $this->base_address . '/manage'
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
    abstract protected function formTableHeader();
    /**
     * Get data for table view 
     */
    protected function getDataForTable()
    {
        $this->toolbarButtons();
        
        $this->formTableHeader();

        $this->_view->table_content = $this->viewTable();
        
        $this->actionButtons();
        
        $this->model->paging()->setBackPage();
    }
    /**
     * common index action to show table
     */
    public function indexAction()
    {
        $this->getDataForTable();

        if($this->_request->isAjax() && $this->_request->isPost())
        {
            $this->_view->useSectionLayout(false);
            echo $this->fetchElementTemplate('table');

            exit;
        }
        
        $this->_view->addJsLangVar( array('are_you_sure' => $this->_lang->table()->are_you_sure) );
    }

    /**
     * common show table
     */
	protected function viewTable()
	{
        if(method_exists($this->model, 'getList'))
        {
            return $this->model->getList(
                    array('limit'=>$this->model->paging()->getItemPerPage())
            );
        }
        else
        {
            return $this->model->get(
                    null,
                    $this->model->paging()->getItemPerPage()
            );
        }
	}

    /**
     * manage entity
     */
    public function manageAction()
    {
        if($this->_request->isPost())
        {// save data
            $this->_view->data = $this->data();
            if( $this->save() )
            {
                $this->_view->addSuccessToSession($this->_lang->admin()->success_manage);
                $this->redirect($this->base_address.'/index/'
                        .Controller::SAVE_FILTER.'/1/page/'
                        .$this->model->paging()->getBackPage());
            }
            else
            {
                $this->addModelErrors();
            }
        }
        else
        {
            $this->_view->data = array();
        }
        if(empty($this->id))
        {// add new entity
            $this->add();
            
            $this->addBreadCrumb(array(
                'title' => $this->_lang->breadcrumb()->create
                        .'&nbsp;'
                        . $this->_lang->{$this->module_lang}()->breadcrumb_create
            ));
        }
        else
        {// edit entity
            $this->edit();
            
            $this->setTitleToBreadcrumb($this->id);
        }
        
        if($this->savebutton)
        {
            $this->addToolbarActionButton(array(
                'title' => $this->_lang->button()->save,
                'js'    => 'save'
            ));
        }
        if($this->backbutton)
        {
            if(isset($_SERVER['HTTP_REFERER']) 
                && !$this->_request->isPost()
            ) {
                $_SESSION['front_manage_backbtn'] = $_SERVER['HTTP_REFERER'];
            }
            if(isset($_SESSION['front_manage_backbtn']))
            {
                $sf = '/'.Controller::SAVE_FILTER.'/1';
                $this->addToolbarActionButton(array(
                    'title' => $this->_lang->button()->discard,
                    'href'  => str_replace($sf, '', $_SESSION['front_manage_backbtn']).$sf
                ));
            }
        }

        $this->_view->addJsLangVar( array('are_you_sure' => $this->_lang->table()->are_you_sure) );
    }

    /**
     * common add action
     */
    protected function add()
    {

    }

    /**
     * common edit action
     */
    protected function edit()
    {
        if(is_callable(array($this->model, 'getById')))
            $data = $this->model->getById($this->id);
        else
            $data = $this->model->getRow( array('id' => $this->id) );
        
        // show 404page if no data was found
        if(!is_array($data))
        {
            $this->error404();
        }
        $this->_view->data += $data;
    }

    /**
     * common save action
     */
    protected function save()
    {
        if($this->validate())
            return $this->model->save($this->data(), $this->id);
        return false;
    }

    /**
     * common delete action
     */
    public function deleteAction()
    {
        $ids = $this->_request->getPost('ids', array(), REQUEST::FILTER_ARRAY);
        
        $ids += ($this->id ? (array)$this->id : array() );

        if($ids)
        {
            if( $this->delete($ids) )
            {
                if($this->_request->isAjax())
                {
                    foreach($ids as &$id)
                        $id = trim($id, "'");
                    $this->ajax->send(Ajax::RESULT_OK, $ids);
                }
                else
                    $this->redirect($this->base_address);
            }
        }
        else
        {
            if($this->_request->isAjax())
            {
                $this->ajax->send(Ajax::RESULT_ERROR, $this->_lang->ajax()->error()->no_data);
            }
            else
                $this->redirect($this->base_address);
        }
    }

    /**
     * common delete
     * @param array $ids (optional) (default: null) array with ids
     * @return bollean
     */
    protected function delete($ids=null)
    {
        $this->model->delete(array('id in'=>$ids));
        return true;
    }
    
    /**
     * <p>Initialize header options: Filter, Order, some html properties.
     * This function can accept 2 or 3 arguments. If first argument is
     * array then function expect 2 arguments, otherwise - 3.
     * If first argument is array then it consist from:
     * <ul>
     *  <li>- field - field name for sql statement or name for write custome filter or order function</li>
     *  <li>- filter - can be:
     *       <ul>
     *          <li>false - if no need filter field at all</li>
     *          <li>text - text input field</li>
     *          <li>some array - for select field. array with key=>value pairse</li>
     *       </ul>
     *  </li>
     *  <li>- order - bool value true to set order functionality or false otherwise</li>
     * </ul>
     * And second argument is array with html options. See example
     * </p>
     * <p>
     * And if first argument is not array than function arguments is:
     * <ul>
     *  <li>- field - field name for sql statement or name for write custome filter or order function</li>
     *  <li>- filter - can be:
     *       <ul>
     *          <li>false - if no need filter field at all</li>
     *          <li>text - text input field</li>
     *          <li>some array - for select field. array with key=>value pairse</li>
     *       </ul>
     *  </li>
     *  <li>- order - bool value true to set order functionality or false otherwise</li>
     * </ul>
     * </p>
     * <p>{@example}<code>
     *  $this->addTableHeader(
     *      array(
     *          $this->_lang->cv()->name => $this->initHeader('name', false, true),
     *          $this->_lang->cv()->date => $this->initHeader(
     *                                              array(
     *                                                  'field'=>'reg_date',
     *                                                  'filter'=>'text',
     *                                                  'order' => false), 
     *                                              array(
     *                                                  'class' => 'someclass',
     *                                                  'style' => 'width:10px;')
     *          )
     *      )
     *  );
     * </code>
     * </p>
     * @param string|array $params field name or array with params of field, filter and order
     * @param mixed $html array with html options
     * @param bool $order (optional d:false) that argument present then first argument
     *  is not array. Flag to set order or not
     * @return array formed parameters
     */
    protected function initHeader($params, $html)
    {
        if(!is_array($params))
        {
            $field = $params;
            $filter = $html;
            $order = func_get_arg(2) ? func_get_arg(2) : false;
            $html = false;
        }
        else
        {
            $field = $params['field'];
            $filter = $params['filter'];
            $order = $params['order'];
        }
        
        if($filter)
            $this->_view->filter = true;
         
        $header = array(
            'filter' => array(
                'key'   => $field,
                'type'  => $filter,
                'order' => $order,
            ),
            'html' => $html
        );
         
        return $header;
    }
    
    /**
     * Initialization button filters (Apply/Clear)
     * 
     * @return array 
     */
    protected function initFilterButton($clear = true)
    {
         $button = $filter = array("filter"
             => array(
                 "apply"  => true,
                 "clear"  => $clear,
                 "type" => "button",
            )
             
         );
         return $button;
    }
}
/* End of file AdminLikePages.php */
/* Location: ./controllers/AdminLikePages.php */
?>