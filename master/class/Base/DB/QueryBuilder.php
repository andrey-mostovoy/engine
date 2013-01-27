<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */


/**
 * class QueryBuilder
 * Creates complete query with using of user-supplied builders
 * 
 * @package		Base
 * @subpackage	Extension
 * 
 * Example 1. Base usage
 * <code>
 * class MyBuilder
 * {
 *      public function formName($value)
 *      {
 *          return array(
 *              'select' =>  '`id`, `name`, `date`',
 *              'from'   => '`user`' //some table name
 *              'where'  => '`name` LIKE "%' . $value . '%"',
 *          );
 *      }
 *      public function formLimit($limit)
 *      {
 *          return array('limit' => implode(',', $limit));
 *      }
 * }
 * 
 * $myBuilder = new MyBuilder();
 * $builder   = new QueryBuilder($myBuilder); //put $myBuilder as handler
 * 
 * //search params
 * $params = array(
 *      'name' => 'person',
 *      'limit' => array(10, 5),
 * );
 * 
 * $query = $builder->getQuery($params); 
 * 
 * echo $query; 
 * //SELECT `id`, `name`, `date` FROM `user` WHERE `name` LIKE %person% LIMIT 10,5 
 * </code>
 * 
 * Example 2. Usage of fields map (adds useful properties to fields)
 * <code>
 * class MyNewBuilder
 * {
 *      public function filterString($params)
 *      {
 *          return array(
 *              'where'  => '`' . $params['name'] . '` LIKE "%' . $params['value'] . '%"',
 *          );
 *      }
 * }
 * 
 * $builder = new QueryBuilder();
 * 
 * //init builder with new handler
 * $builder->setHandler(new MyNewBuilder());
 * 
 * //init default "select" and "from" params
 * $builder->append(array(
 *      'select' =>  '`id`, `name`, `description`, `date`',
 *      'from'   => '`book`'));
 *  
 * //append map with "group" property
 * $builder->appendFieldMap(array(
 *      'filter' => array(
 *          QueryBuilder::PROP_IS_SECTION = true,
 *          QueryBuilder::PROP_GROUP => array(
 *              'string' => array('name', 'description') //'string' it's a group name
 *          )
 *      ))
 * );
 * 
 * $params = array(
 *      'filter' => array( 
 *          'name'        => 'someName'
 *          'description' => 'desc'
 *      )
 * );
 * 
 * $query = $builder->getQuery($params);
 * 
 * echo $query;
 * //SELECT `id`, `name`, `description`, `date` FROM `book` 
 * //WHERE `name` LIKE "%someName%" AND `description LIKE "%desc%"
 * </code>
 */
class QueryBuilder
{
    //Sections (methods prefix in the same time)
    const FILTER  = 'filter';
    const APPEND  = 'append';
    const BUILDER = 'form'; //default
    
    //Output formatting
    const FORMAT_STRING = 'string';
    const FORMAT_ARRAY  = 'array';
    
    //Sql keys used in query
    const SQL_SELECT = 'select';
    const SQL_FROM   = 'from';
    const SQL_JOIN   = 'join';
    const SQL_WHERE  = 'where';
    const SQL_ORDER  = 'order';
    const SQL_GROUP  = 'group';
    const SQL_HAVING = 'having';
    const SQL_LIMIT  = 'limit';
    
    //Fields properties
    const PROP_IS_SECTION  = 'prop_is_section';
    const PROP_CHECK_EMPTY = 'prop_check_empty'; 
    const PROP_GROUP       = 'prop_group';
    
    /**
     * Contains current request parameters 
     * 
     * @var array 
     */
    protected $_request = array();
    /**
     * 
     * @var mixed
     */
    private $_process_new_param;
    /**
     * Contains current query
     * Initial values define right order
     * 
     * @var array 
     */
    protected $_query = array();
    /**
     * Query after formating
     * 
     * @var array
     */
    protected $_queryFormated = array();
    /**
     * Registered aliases
     * 
     * @var array  
     */
    protected $_alias = array();
    /**
     * Handler that supply building functions
     * Notice: ALL building methods in handler must be PUBLIC
     * 
     * @var object 
     */
    protected $_handler;
    /**
     * Fields property map
     * 
     * @var array
     */
    protected $_fieldMap = array();
    /**
     * Default property map
     * 
     * @var array
     */
    protected $_defaultFieldMap = array();

    private static $instance;
    
    /**
     * Init
     * 
     * @param object $handler 
     */
    private function __construct($handler = null)
    {
        $this->clear()->setHandler($handler);
    }
    
    /**
     *
     * @param Model $handler
     * @return QueryBuilder 
     */
    public static function getInstance($handler = null)
    {
        return self::$instance = self::$instance ? self::$instance : new self($handler);
    }
    
    /**
     * Gets complete query
     * 
     * @param array $params  reserved for sql: limit, order, having, group
     *                       reserved sections (can contain sub-params): filter, append 
                             all params are optional 
     * @param array $type  defines output format
     * @return string|array
     */
    public function getQuery($params, $type = self::FORMAT_STRING)
    {
        return $this->processRequest($params)->format($type)->getFormated();
    }
    
    /**
     * Gets complete query
     * 
     * @param array $params  reserved for sql: limit, order, having, group
     *                       reserved sections (can contain sub-params): filter, append 
                             all params are optional 
     * @param array $type  defines output format
     * @return string|array
     */
    public function getQueryWithParams($params, $type = self::FORMAT_STRING)
    {
        return array(
            'query' => $this->processRequest($params)->formatWithParams($type)->getFormated(),
            'params'=> $this->get(),
        );
    }
    
    /**
     * Sets building handler
     * 
     * @param object $handler
     * @return QueryBuilder 
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
        return $this;
    }
    
    /**
     * Gets current handler
     * 
     * @return object 
     */
    public function getHandler()
    {
        return $this->_handler;
    }
    
    /**
     * Sets request params
     * 
     * @param array $params
     * @return QueryBuilder 
     */
    public function setRequest($params)
    {
        $this->_request = $params;
        return $this;
    }
    
    /**
     * Gets request
     * 
     * @return array 
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Appends some params to request
     * 
     * @param array $params
     * @return QueryBuilder 
     */
    public function appendRequest($params)
    {
        if (!is_array($params)) return $this;
        
        $this->_request = $this->mergeArrays($this->_request, $params);
        return $this;
    }
    
    /**
     * Sets query params
     * 
     * @param array $params
     * @return QueryBuilder
     */
    public function set($params)
    {
        $this->_query = $params;
        return $this;
    }
    
    /**
     * Gets query
     * 
     * @return array|string 
     */
    public function &get()
    {
        return $this->_query;
    }
    
    /**
     * Appends params to query
     * 
     * @param array $params
     * @return QueryBuilder 
     */
    public function append($params)
    {
        if (!is_array($params)) return $this;
        
        foreach($params as $key => &$param)
        {
            if(is_array($param))
            {
                if($this->isSection($key))
                {
                    $this->_process_new_param[$key] += $param;
                }
                else
                {
                    $this->_query[$key] = $this->mergeArrays((array)$this->_query[$key], $param);
                }
            }
            elseif(is_null($this->_query[$key]))
            {
                $this->_query[$key][0] = $param;
            }
            else
            {
                $this->_query[$key][] = $param;
            }
        }

        return $this;
    }
    
    /**
     * Clears builder
     * 
     * @return QueryBuilder 
     */
    public function clear()
    {
        return $this->clearQuery()->clearAlias()->clearFormated()->restoreFieldMap();
    }
    
    /**
     * Clears query container
     * 
     * @return QueryBuilder
     */
    public function clearQuery()
    {
        $this->_query = array(
            self::SQL_SELECT => null,
            self::SQL_FROM   => null,
            self::SQL_JOIN   => null,
            self::SQL_WHERE  => null,
            self::SQL_GROUP  => null,
            self::SQL_HAVING => null,
            self::SQL_ORDER  => null,
            self::SQL_LIMIT  => null,
        );
        return $this;
    }
    
    /**
     * Clears alias container
     * 
     * @return QueryBuilder 
     */
    public function clearAlias()
    {
        $this->_alias = array();
        return $this;
    }
    
    /**
     * Clears formated query container
     * 
     * @return QueryBuilder 
     */
    public function clearFormated()
    {
        $this->_queryFormated = array();
        return $this;
    }
    
    /**
     * Registers new table alias
     * Notice:
     *  Warning will be triggered if alias or name already exist. 
     *  But use this feature only for debugging (the best is check getAlias() before addAlias())
     * 
     * @param string $name
     * @param string $alias
     * @return QueryBuilder 
     */
    public function addAlias($name, $alias)
    {
        if (!empty($this->_alias[$name]))
        {
            trigger_error(__CLASS__ .': Name `' . $name . '` already used');
            return $this;
        }
        elseif (in_array($alias, $this->_alias))
        {
            trigger_error(__CLASS__ .': Alias `' . $alias . '` already used');
            return $this;
        }
        
        $this->_alias[$name] = $alias;
        return $this;
    }
    
    /**
     * Gets alias for name
     * 
     * @param string $name
     * @return string|null
     */
    public function getAlias($name)
    {
        return !empty($this->_alias[$name]) ? $this->_alias[$name] : null;
    }
    
    /**
     * Sets query params
     * 
     * @param array $params
     * @return QueryBuilder
     */
    public function setFormated($params)
    {
        $this->_queryFormated = $params;
        return $this;
    }
    
    /**
     * Gets formated query
     * 
     * @return array|string 
     */
    public function &getFormated()
    {
        return $this->_queryFormated;
    }
    
    /**
     * Sets field map
     * 
     * @param array $map
     * @return QueryBuilder
     */
    public function setFieldMap($map)
    {
        $this->_fieldMap = $map;
        return $this;
    }
    
    /**
     * Gets field map
     * 
     * @return array
     */
    public function getFieldMap()
    {
        return $this->_fieldMap;
    }
    
    /**
     * Appends data to field map
     * 
     * @param array $map
     * @return QueryBuilder
     */
    public function appendFieldMap($map)
    {
        $this->_fieldMap = $this->mergeArrays($this->_fieldMap, $map);
        return $this;
    }
    
    /**
     * Restore field map to default
     * 
     * @return QueryBuilder
     */
    public function restoreFieldMap()
    {
        return $this->setFieldMap($this->getDefaultFieldMap());
    }
    
    /**
     * Sets default field map
     * 
     * @param array $map
     * @return QueryBuilder
     */
    public function setDefaultFieldMap($map)
    {
        $this->_defaultFieldMap = $map;
        return $this;
    }
    
    /**
     * Gets default field map
     * 
     * @return array
     */
    public function getDefaultFieldMap()
    {
        return $this->_defaultFieldMap;
    }
    
    /**
     * Processes request params
     * 
     * @param array $request for each key in request (except sections) is called a related method
     *                       method: <section><KeyInUppercase>()
     *                       default is section "form"
     * @return QueryBuilder 
     */
    public function processRequest($request)
    {
        if (empty($request) || !is_array($request)) return $this;
        
        $this->setRequest($request);
        
//        foreach($request as $section => &$param)
        foreach($this->_request as $section => &$param)
        {
            if ($this->isSection($section))
            {
                $this->_process_new_param[$section]=array();
                foreach($param as $name => &$value)
                {
                    if(!$this->checkEmpty($value, $name, $section)) continue;
                   
                    $default = $this->getFieldGroup($name, $section);
                    $this->callMethod($name, $value, $section, $default);
                }
                if(!empty($this->_process_new_param[$section]))
                {
                    foreach($this->_process_new_param[$section] as $name => &$value)
                    {
                        if(!$this->checkEmpty($value, $name, $section)) continue;

                        $default = $this->getFieldGroup($name, $section);
                        $this->callMethod($name, $value, $section, $default);
                    }
                    $this->appendRequest(array($section=>$this->_process_new_param[$section]));
                    unset($this->_process_new_param[$section]);
                }
            }
            else
            {
                $this->callMethod($section, $param, self::BUILDER);
            }
        }
        return $this;
    }
    
    /**
     * Gets related with field table
     * 
     * @param string $name  field name
     * @param string $section
     * @return string|null
     */
    public function getFieldTableAlias($name, $section)
    {
        $group = $this->getFieldGroup($name, $section);
        
        $table = !empty($group['table']) ? $group['table'] : null;
        return $this->getAlias($group['table']);
    }
    
    /**
     * Merge two arrays recursively
     * Don't create new subarrays (not like array_merge_recursive)
     * 
     * @param array $arr1
     * @param array $arr2
     * @return array
     */ 
    public function mergeArrays($arr1, $arr2)
    {
        foreach((array)$arr2 as $key => $value)
        {  
            if (isset($arr1[$key]) && !is_array($arr1[$key]) && !is_int($key))
            {
                $arr1[$key] = $value;
            }
            elseif(array_key_exists($key, (array)$arr1) && is_array($value))
            {
                $arr1[$key] = $this->mergeArrays($arr1[$key], $arr2[$key]);
            }
            elseif (is_int($key) && !in_array($value, (array)$arr1))
            {
                $arr1[] = $value;
            }
            else
            {
                $arr1[$key] = $value;
            }  
        }
      
        return $arr1;
    }
    
    /**
     * Corrects params and format
     * 
     * @param sting $type  see const section "format"
     * @return QueryBuilder
     */
    protected function format($type)
    {
        $query = $this->get();

        $this->combine(',', array_reverse($query[self::SQL_SELECT]), '*')
            ->combine(',', $query[self::SQL_FROM])
            ->combine(' ', $query[self::SQL_JOIN])
            ->combine(' AND ', $query[self::SQL_WHERE] , '1=1')
            ->combine(',', $query[self::SQL_GROUP])
            ->combine(',', $query[self::SQL_ORDER])
            ->combine(' AND ', $query[self::SQL_HAVING])
            ->combine(',', $query[self::SQL_LIMIT])
            ->setFormated($query);
        
        if (self::FORMAT_STRING == $type)
        {
            $this->appendStatements()->combine("\n", $this->getFormated());
        }
    
        return $this;
    }
    
    /**
     * Corrects params and format
     * 
     * @param sting $type  see const section "format"
     * @return QueryBuilder
     */
    protected function formatWithParams($type)
    {
        $query = $this->get();
        
        $query = array(
            self::SQL_SELECT => $query[self::SQL_SELECT],
            self::SQL_FROM => $query[self::SQL_FROM],
            self::SQL_JOIN => $query[self::SQL_JOIN]
        );
        
        $this->combine(',', $query[self::SQL_SELECT], '*')
            ->combine(',', $query[self::SQL_FROM])
            ->combine(' ', $query[self::SQL_JOIN])
            ->setFormated($query);
        
        if (self::FORMAT_STRING == $type)
        {
            $this->appendStatements()->combine("\n", $this->getFormated());
        }
    
        return $this;
    }
    
    /**
     * Appends statements to current query
     * 
     * @return QueryBuilder 
     */
    protected function appendStatements()
    {
        $statements = array(
            self::SQL_SELECT => 'SELECT',
            self::SQL_FROM   => 'FROM',
            self::SQL_WHERE  => 'WHERE',
            self::SQL_GROUP  => 'GROUP BY',
            self::SQL_ORDER  => 'ORDER BY',
            self::SQL_HAVING => 'HAVING',
            self::SQL_LIMIT  => 'LIMIT',
        );
        
        if (!$query = $this->getFormated()) return $this;
          
        foreach ($query as $name => &$entry)
        {
            if (!empty($statements[$name]) && !empty($entry))
            {
                $entry = $statements[$name] . ' ' . $entry;
            }
        }
         
        return $this->setFormated($query);
    }
    
    /**
     * Combines params in array
     * 
     * @param array  $glue
     * @param array  $query
     * @param string $default  used if query empty
     * @return QueryBuilder 
     */
    protected function combine($glue, &$query = null, $default = '')
    {
        if (empty($query) && isset($default))
        {
            $query = $default;
        }
        elseif (isset($glue) && !empty($query) && is_array($query))
        {
            $query = array_filter($query);
            $query = implode($glue."\r\n", $query);
        }
        
        return $this;
    }
    
    /**
     * Checks is name 
     * @param string $name
     * @return bool
     */
    protected function isSection($name)
    {
        return (bool)$this->getFieldProperty(self::PROP_IS_SECTION, $name);
    }
    
    /**
     * Checks is $value not empty against field properties
     * 
     * @param mixed  $value
     * @param string $name
     * @param string $section = null
     * @return bool
     */
    protected function checkEmpty($value, $name, $section = null)
    {
        $property = $this->getFieldProperty(self::PROP_CHECK_EMPTY, $name, $section);
        
        if (is_null($property))
        {
            $property = $this->getFieldProperty(self::PROP_CHECK_EMPTY, $section);
        }
        
        if ($property && (!isset($value) || false === $value 
            || (is_string($value) && '' === trim($value))))
        {
            return false;
        } 
        return true;
    }
    
    /**
     * Gets field group
     * 
     * @param string $name
     * @param string $section
     * @return string|null
     */
    protected function getFieldGroup($name, $section)
    {
        $groups = $this->getFieldProperty(self::PROP_GROUP, $section);
        
        if (empty($groups)) return null;
        
        foreach ($groups as $groupName => $group)
        {
            foreach ($group as $table => $block)
            { 
                if (in_array($name, $block)) 
                {
                    if (is_string($table))
                    {
                        return array(
                            'name'  => $groupName,
                            'table' => $table,
                        );
                    }

                    return $groupName;
                } 
            }  
        }
        
        return null;
    }
    
    /**
     * Gets field property
     * 
     * @param string $property
     * @param string $name
     * @param string $section = null
     * @return mixed
     */
    protected function getFieldProperty($property, $name, $section = null)
    {
        if ($section)
        {
            return isset($this->_fieldMap[$section][$name][$property]) ?
                $this->_fieldMap[$section][$name][$property] : null;
        }
        
        return isset($this->_fieldMap[$name][$property]) ?
            $this->_fieldMap[$name][$property] : null;
    }
    
    /**
     * Calls related with name + section method
     * 
     * @param string $name
     * @param mixed  $param
     * @param string $section 
     */
    protected function callMethod($name, &$param, $section, $default = null)
    {
         $result =  $this->callRelatedMethod(
                $this->getMethodName($name, $section),
                $param
            );
         
         if (null === $result && !empty($default))
         { 
            $param = array('name' => $name, 'value' => $param);
            
            if (is_array($default))
            {
                $param['table'] = $default['table'];
                $default = $default['name'];
            }
            $result = $this->callRelatedMethod(
               $this->getMethodName($default, $section),
               $param
            );
         }
         
         return $this->append($result);
    }
    
    /**
     * Calls related with name method
     * 
     * @param string $name
     * @param array  $params
     * @return array|null 
     */
    protected function callRelatedMethod($name, $params = null)
    {
        if (empty($name))
        {
            return null;
        }
     
        if (is_callable(array($this->getHandler(), $name)))
        { 
            return $this->getHandler()->$name($params);
        }

        return null;
    }
    
    /**
     * Forms method name
     * 
     * @param string $name
     * @param string $section
     * @return string 
     */
    protected function getMethodName($name, $section)
    {
        $name = implode('', array_map('ucfirst', (array)explode('_', $name)));
        return $section . $name;
    }
}