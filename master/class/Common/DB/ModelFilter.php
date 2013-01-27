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

Loader::loadClass('Common_DB_ModelValidation');

/**	
 * class ModelFilter.
 * Containing common methods and class properties for work with filter capability.
 * Also methods of that class used by {@see QueryBuilder}
 * 
 * @package		Base
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class ModelFilter extends ModelValidation
{
    /**
     * Indicate using filter logic of
     * formSearch method
     * @var bool
     */
    public $use_filter = true;
    /**
     * Gets filter groups
     * 
     * @return array
     * {@example}<p>
     * <code>
     * return array(
     *  'digital' => array( //group name
     *      'content' => array('id', 'status'), //'content' is used for alias
     *      array('profile_name'), //alias is not used
     *  ),
     *  'string' => array(
     *      'profile' => array('name', 'city'),
     *  )
     * );
     * </code>
     * </p>
     */
    protected function getFilterGroups() {return array(); }
    /**
     * Forms current filter (search) params
     * 
     * @return array|null
     */
    protected final function formatFilter($params)
    {
        if(App::request()->getParam('__filter_clear', false, Request::FILTER_BOOL))
        {
            $filter = null;
        }
        elseif(isset($params['filter']) && isset($this->filter))
        {
            $filter = $this->builder->mergeArrays($params['filter'], $this->filter);
        }
        elseif(isset($this->filter) && !empty($this->filter))
        {
            $filter = $this->filter;
        }
        elseif(isset($params['filter']) && isset($_SESSION[$params['session']['save']][Controller::FILTER]))
        {
            $filter = $this->builder->mergeArrays(
                    $_SESSION[$params['session']['save']][Controller::FILTER],
                    $params['filter']
            );
        }
        elseif(isset($params['filter']))
        {
            $filter = $params['filter'];
        }
        else
        {
            $filter = null;
        }

        if (isset($_SESSION[$params['session']['save']][Controller::FILTER]) && empty($filter))
        {
            return $filter = $_SESSION[$params['session']['save']][Controller::FILTER];
        }
       
        if($params['session']['save'])
        {
            return $_SESSION[$params['session']['save']][Controller::FILTER] = $filter;
        }
        else
            return $filter;
    }
    /**
     * Forms query for digital values
     * 
     * @param array  $params
     * @return array
     */ 
    public final function filterDigital($params)
    {
        $alias = $this->getParamAlias($params);

        $param = array_filter((array)$params['value'], 'is_numeric');
        
        if (empty($param)) return null;
        
        $query[$alias . $params['name'] . ' in'] = $param;
        
        if (empty($alias))
        {
            return array('having' => $query);
        }
        
        return array('where' => $query);
    }
    /**
     * Forms query for string values that no need to "LIKE"
     * 
     * @param array  $params
     * @return array
     */ 
    public final function filterSingle($params)
    { 
        $alias = $this->getParamAlias($params);
        
        $query[$alias . $params['name'] . ' in'] = array_map(array($this, '_escape'), (array)$params['value']);
        
        if (empty($alias))
        {
            return array('having' => $query);
        }
        
        return array('where' => $query);
    }
    /**
     * Forms query for string values
     * 
     * @param array  $params
     * @return array
     */ 
    public final function filterString($params)
    {
        $alias = $this->getParamAlias($params);

        $where = array();

        foreach ((array)$params['value'] as $value)
        {
            $where[$alias.$params['name'].' like'] = '%'.$value.'%';
        }
        
        if (empty($alias))
        {
            return array('having' => array(
                'or' => $where
            ));
        }
        
        return array('where' => array(
                'or' => $where
            ));
    }
    /**
     * Forms query for fields that may contain plural values
     * Delimeter is ','
     * 
     * @param array $params
     * @return array
     */ 
    public function filterPlural($params)
    {
        $alias = $this->getParamAlias($params);
        
        $query = '(';
        $index = 0;
        foreach ((array)$params['value'] as $value)
        {
            if (0 != $index)
            {
                $query .= ' OR ';
            }
            
            $query .= $alias . $params['name'] . ' REGEXP "(^|[,^\.]+)' 
                   . $this->_escape($value, true) . '([,^\.]+|$)"';
            $index++;
        }
        $query .= ')';
        
        if (empty($alias))
        {
            return array('having' => $query);
        }
        
        return array('where' => $query);
    }
}

/* End of file ModelFilter.php */
/* Location: ./class/Common/DB/ModelFilter.php */
?>
