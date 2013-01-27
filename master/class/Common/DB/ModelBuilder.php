<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Common
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass('Common_DB_ModelOrder');

/**	
 * class ModelBuilder.
 * Containing common methods and class properties for work with {@see QueryBuilder} class
 * that build sql statement from array of arguments. Functions of that class
 * help QueryBuilder make query depend on request data.
 * {@uses QueryBuilder}
 * 
 * @package		Base
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class ModelBuilder extends ModelOrder
{
    /**
     * Query builder class instance
     * @var QueryBuilder
     */
    protected $builder = null;
    
    protected function __construct()
    {
        parent::__construct();
        
        //init query builder
        $this->builder = QueryBuilder::getInstance($this);
        $this->builder->setDefaultFieldMap(array(
            'filter' => array(
                QueryBuilder::PROP_IS_SECTION => true),
            'append' => array(
                QueryBuilder::PROP_IS_SECTION  => true,
                QueryBuilder::PROP_CHECK_EMPTY => true,
            ))
       );
    }
    /**
     * Append data to array
     * @deprecated Not in use?
     * @param array $data multidimensional array
     * @param array $content will be appended
     * @param string $fieldName "key" in $data with unique value in that row content will be appended
     * @param string $label with this name $content will be appended
     */ 
    protected function appendData(&$data, $content, $fieldName, $label)
    {
        foreach ($data as &$entry)
        {
            if (empty($content[$entry[$fieldName]])) continue;
            
            $entry[$label] = $content[$entry[$fieldName]];
        }
    }
    /**
     * Merge specific fields from multidimensional array by comma
     * 
     * @param array $data array(0 => array('fieldName' =>), 1 => array('fieldName'=>))
     * @param string $field_name
     * @param string $separator if not specified - array will not be imploded
     * @return string|array merged fields
     */ 
    protected function mergeFields($data, $field_name, $separator = ',')
    {
        $merged = array();
        
        foreach ($data as $entry)
        {
            if (!empty($entry[$field_name]))
            {
               
                $merged[] = $entry[$field_name];
            }
        }
        
        if ($separator)
        {
            return implode($separator, $merged);
        }
    
        return $merged;
    }
    /**
     * Group multidimensional array by specified key
     * 
     * @param array $data
     * @param string $key some key in array row
     * @param string $value some key, will be used as value
     * @param bool $notUniqueValue if true - array will be multidimensional
     * @return array
     */ 
    public function groupArray($data, $key, $value = null, $notUniqueValue = false)
    {
        if (empty($data) || !is_array($data)) return $data;
        
        $group = array();
        foreach ($data as $entry)
        {
            if (isset($entry[$key]))
            {
                if ($value)
                {
                    $entryValue = $entry[$value];
                }
                else
                {
                    $entryValue = $entry;
                }
                
                if ($notUniqueValue)
                {
                    $group[$entry[$key]][] = $entryValue;
                }
                else
                {
                    $group[$entry[$key]] = $entryValue;
                }
            }
        }
        
        return $group;
    }
    /**
     * Generic search 
     * 
     * @param array $params keys:
     *  - filter - array 
     *  - append - array
     *  - order  - array
     *  - limit  - int
     * @return array
     */ 
    public function find($params)
    {
//        $qp = array();
//        if (isset($params['limit']) && !empty($params['limit']))
//        {
//            $qp = $this->formLimit($params['limit']);
//        }
//        unset($params['limit']);
      
        $this->builder->clear();
        
        $this->bindTableAlias();
        $this->formBuilderParams($params);
       
        $stm = $this->builder->setHandler($this)
            ->appendFieldMap($this->getFieldMap())
            ->getQueryWithParams($params);

        if($this->execute_stmt)
            return $this->_selectData($stm['query'], $stm['params']);
        else
        {
            $this->execute_stmt = true;
            return $stm;
        }
    }
    
    protected function bindTableAlias(){}
    
    /**
     * Sets to builder init params before search
     * 
     * @param array $params  the same as for find()
     */
    protected function formBuilderParams(&$params) {}
    
    /**
     * Gets alias for params
     * 
     * @param array $params
     * @return string
     */
    protected function getParamAlias($params)
    {
        $alias = '';
        if (!empty($params['name']))
        {
            $alias = $this->builder->getFieldTableAlias($params['name'], 
                QueryBuilder::FILTER);
        }
        
        return $alias ? $alias . '.' : '';
    }
    
    /**
     * Gets fields map for builder
     * 
     * @return array
     */
    protected function getFieldMap()
    {
        return array(
            QueryBuilder::FILTER => array(
                QueryBuilder::PROP_GROUP => $this->getFilterGroups(),
                QueryBuilder::PROP_CHECK_EMPTY => true,
            ),
        );
    }
    
    /**
     * Converts search params for model_filter
     * 
     * @return array
     */
    public final function formFind($params)
    {
        if(!isset($params['session']['save']))
            $params['session']['save'] = $this->getName();
        
        if($params['session']['save'] !== false)
        {
            $this->data(App::controller()->data());
            
            $isPaging = App::request()->getParam('page', false, Request::FILTER_BOOL);
            $isAction = App::request()->getPost(Controller::FILTER, false, Request::FILTER_BOOL)
                      || App::request()->getPost(Controller::ORDER, false, Request::FILTER_BOOL);
            $isSaveFilter = App::request()->getParam(Controller::SAVE_FILTER, App::request()->getPost(Controller::SAVE_FILTER, false, Request::FILTER_BOOL), Request::FILTER_BOOL);
        
            if (!$isPaging && !$isAction && !$isSaveFilter)
            {
                $_SESSION[$params['session']['save']] = null;
            }

            if($this->use_filter)
            {
                $this->filter(App::controller()->filter());
                $params['filter'] = $this->formatFilter($params);
            }
            if($this->use_order)
            {
                $this->order(App::controller()->order());
                $params['order']  = $this->formatOrder($params);
            }
        }
        return $params;
    }
    
    /**
     * form 'join' sql statement
     * @param array $params
     * @return array 
     */
    public final function formJoin($params)
    {
        if(!empty($params))
        {
           return array("join"=> $params);
        }
    }
    
    /**
     * form group sql statement
     * @param array $group
     */
    public function formGroup($group)
    {
        return array('group' => $this->builder->getAlias('content').'.id');
    }
    
    /**
     * Form order sql statement
     * @param string $order
     */
    public final function formOrder($order)
    {
        if (empty($order['type'])) return '';
        $dir = !empty($order['dir']) ? $order['dir'] : 'ASC';
        
        $method = 'formOrder' . implode('', array_map('ucfirst', explode('_', $order['type'])));

        if (method_exists($this, $method))
        {
            $order_str = $this->$method($dir);
        }
        else
        {
            $alias = $this->builder->getFieldTableAlias($order['type'], QueryBuilder::FILTER);

            $alias = $alias ? $alias . '.' : '';
            $order_str = $alias .  $order['type'] . ' ' . $order['dir'];
        }
        return array('order' => $order_str);
    }
    
    /**
     * Forms limit
     * Not used in query builder
     * @deprecated But stile used
     * @param int|array $limit
     * @return array 
     */
    public final function formLimit($limit)
    {
//        $limit = is_numeric($limit) ? 
//            $this->paging()->getLimitArray($limit) : $limit;
        return array('limit' => $this->paging()->getLimitArray($limit));
    }
}

/* End of file ModelBuilder.php */
/* Location: ./class/Common/DB/ModelBuilder.php */
?>
