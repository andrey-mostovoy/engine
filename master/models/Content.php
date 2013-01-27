<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * class modelContent
 * base class for working with site content
 * 
 * @package		Project
 * @subpackage	Models
 * @category	Database
 */
abstract class modelContent extends Model
{
    const APPEND_ARRAY = 3;

    // variables for preparing query
    /**
     * @deprecated
     * @var type 
     */
    private $sql = array(); //deprecated

    /**
     * alias in query for content meta table
     */
    const ALIAS_CM = 'cm';
    /**
     * alias in query for curent content table
     */
    const ALIAS_CC = 'cc';

    // for overiding in child classes
    /**
     * @deprecated
     * @var type 
     */
    protected $meta_type   = '';
    protected $meta_source = '';
    protected $table_map;

    //content type
    const USERS    = 'user';
    const LANG     = 'lang';
    const SETTINGS = 'settings';
    
    
    
    private $meta_data = array();

    // private rules for content meta table
    private  $rules = array(
        'common' => array(
        ),
    );

    protected function _run()
    {
        $this->formTableMap();
    }
    
    protected function setName()
    {
        return '';
    }
    
    /**
     * create table map structure
     */
    protected function formTableMap()
    {
        $this->table_map = array();
    }
    
    /**
     * Set rules for current content type
     */
    protected function addContentRules()
    {
        if(isset($this->_rules[$this->meta_type]))
        {
            $this->rules[$this->meta_type] = $rules;
        }
    }

    /**
     * Get content by specific params
     * 
     * @param array $params all fields are optional
     *  - filter
     *      - array with info that need append. Format: key=>value.
     *          key will use in function name (format: filter{Key} ) which will be callen
     *          Function must return array with some of following parameters:
     *          select, join, where, group, having, order
     *  - append
     *      - array with info that need append. Format: key=>value.
     *          key will use in function name (format: append{Key} ) which will be callen
     *          Function must return array with some of following parameters:
     *          select, join, where, group, having, order
     *  - append_after 
     *      - array with setup of data which need to add to result after query done.
     *      Format: key => value.
     *          key - will use as append key to result array, example: 'point' key name
     *                  will be using as key in result array: $result['point']...
     *          value - can be 
     *              - bool: true - will use self::APPEND_ARRAY constant in appendContent method as $parent_key
     *                      false - method will not call
     *              - string: result array key name to use as $parent_key in appendContent method
     *              - array: 0 - model object handler, example: App::model('Point', false)
     *                       1 - [optional] method name to call in model object handler. By default call 'modelAppend' method for current model and 'modelAppendAfter' for other model
     *                       field - [optional] see declaration of use value as string
     *      If value will be not array with another model handler, will be use method 'appendAfter%Key%' of current model
     *  - join - string with join statement
     *  - where - array with where statement
     *  - order - array with keys type and dir. Example array('type'=>'cc.name','dir'=>'ASC'),
     *  - limit - int
     *  - session - optional array with settings for work with session
     *      - save - key name to save in session or false if not save
     *  - perm_section   - string  uses in defineContentPermissions() self::PERM_SECTION
     *  - perm_is_single - bool  uses in defineContentPermissions() self::PERM_IS_SINGLE
     *  - perm_skip      - bool  do not define permissions
     */
    protected function getContent($params)
    {
        /**
         * Order of the params key related to order of useg before create query
         */
        $def_params = array(
            'append' => array(
//                'related_content' => true,
            ),
            'filter' => array(),
            'order'  => array(),
            'where'  => '',
            'limit'  => array(),
            'no_last'=> false,
            'group'  => array(),
            'having' => null,
        );

        //get params
        $params = $this->builder->mergeArrays($def_params, $params);

        $params = $this->formFind($params);
        App::controller()->setViewFilterOrder($params);
        
        $result = $this->find($params);
        
        //append permissions
        if ($this->use_permission && empty($params['perm_skip']))
        {
            $perm_section = !empty($params[self::PERM_SECTION]) ? 
                $params[self::PERM_SECTION] : 'content';
            $is_single = !empty($params[self::PERM_IS_SINGLE]);
            $result = $this->defineContentPermissions($result, $perm_section, $is_single);
        }
        
        if(!empty($params['append_after']))
        {
            foreach($params['append_after'] as $key => &$val)
            {
                if($val)
                {
                    $params['_append_key'] = $key;
                    $result = $this->appendContent(
                            $key,
                            $result,
                            (is_bool($val) ? self::APPEND_ARRAY : $val),
                            0,
                            $params);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Sets to builder init params before search
     * 
     * @param array $params  the same as for find()
     */
    protected function formBuilderParams(&$params) 
    {
        $this->builder->addAlias('content', self::ALIAS_CC)
                    ->append(array(
                        'select' => $this->formDefaultSelect(), 
                        'from'   => '`' . $this->_name . '` AS `'.$this->builder->getAlias('content').'` '
        ));
    }
    
    protected function formDefaultSelect()
    {
        return $this->builder->getAlias('content').'.*';
    }
    
    /**
     * Gets filter map
     * 
     * @return array
     */
    protected function getFilterGroups()
    { 
        $groups = array(
            'digital' => array(
                'content' => array('id'),
            )
        );
        
        return $this->builder->mergeArrays($groups, $this->getContentFilter());
    }
    
    /**
     * Gets filter map for content tables
     * 
     * @return array|null  the same as for getFilterGroups() 
     */
    protected function getContentFilter()
    {
        return null;
    }

    /**
     * retriev current content params
     * @return array
     * @deprecated  use $this->builder->getRequest()
     */
    protected function getContentParams()
    {
        return $this->sql;
    }

    /**
     * Return content table name. It check for table_map variable for needed content table
     * and if find it - return its name, otherwise it return 
     * $this->_name variable content
     * @param string $name type of content, that is a key in table_map
     * @return string
     */
    private function contentTableName($name)
    { 
        if(is_string($name))
        {
            $name = str_replace('\'', '', $name);
            return isset($this->table_map[$name]) ? $this->table_map[$name] : $this->_name;
        }
        return $this->_name;
    }
    
    /**
     * Delete content meta and related content recursively
     *
     * @param int|array $metaId
     * @param array $exclude list of exclude types to delete
     * @param bool
     */
    public function deleteContent($meta_id, $exclude=array())
    {        
        $meta_id = array_filter((array)$meta_id);

        if (empty($meta_id)) return false;
        
        // set variable to determinate recursion
        if(!isset($this->ids_to_delete))
        {
            $this->ids_to_delete = array();
        }
        
        // condition to exit from recursion
        if(count($this->ids_to_delete) != count($meta_id))
        {
            $this->ids_to_delete = $meta_id;
            
            //find related content
            $query = 'SELECT '.self::FIELD_META_ID.', '.self::FIELD_CONTENT_ID.', '.self::FIELD_TYPE.'
                     FROM `' . $this->_content_meta . '`
                     WHERE ( '.self::FIELD_META_ID.' in ('.implode(',', $meta_id).') OR '.self::FIELD_PARENT_ID.' in ('.implode(',', $meta_id).') )
            ';
            if(!empty($exclude))
            {
                $query .= ' AND '.self::FIELD_TYPE.' NOT IN (';
                foreach($exclude as $e)
                {
                    $query .= $this->_escape($e).',';
                }
                $query = rtrim($query, ',') . ')';
            }

            $content = $this->_selectData($query);
    
            if(!empty($content))
            {
                if(!isset($this->content_to_delete))
                {
                    $this->content_to_delete = array();
                }
                // fill in content to delete
                $this->content_to_delete += $content;
                $child = array();
                foreach($content as $c)
                {
                    $child[] = $c[self::FIELD_META_ID];
                }
                // call this function recursively while not found all childrens
                $this->deleteContent($child);
                
            }
        }

        if(!empty($this->content_to_delete))
        {
            // ids to delete from current content table
            $ids = array();
            foreach($this->content_to_delete as $content)
            {
                
                if(isset($this->table_map[$content[self::FIELD_TYPE]]))
                {
                    $ids[$content[self::FIELD_TYPE]][] = $content[self::FIELD_CONTENT_ID];
                }
            }
           
            // delete from current content table
            foreach($ids as $table => $id)
            {
                // delete from table content
                $this->_deleteData(array('id IN '=>$id), $this->table_map[$table]);
            }
        }
        if(!empty($this->ids_to_delete))
        {
            // delete from meta database
            $this->_deleteData(array(self::FIELD_META_ID.' IN' => $this->ids_to_delete), $this->_content_meta);
        }

        return true;
    }
    
    /**
     * Append to data existed content
     *
     * @param string $ctype part of method name i.e. "comment" mean "appendAfterComment".
     * If append from other model - using modelAppendAfter name
     * @param array $data multidimensional content
     * @param string|array $parent_field key in data array, or self::APPEND_ARRAY constant
     * to pass all data array, or array in format array(modelHandle, method name)
     * @param int $visitor_id current member profile id
     * @param array $params same as for $this->getContent() and 
     *  'need_reverse' - (bool) reverse comments order after selecting
     */
    protected final function appendContent($ctype, $data, $parent_field=self::APPEND_ARRAY, $visitor_id = 0, $params = array())
    {
        $visitor_id = (int) $visitor_id;
        if($visitor_id === 0 && !App::user()->isAdmin())
        {
            $visitor_id = App::user()->isAuth() ? (int)App::user()->id : 0;
        }
        
        $call=array();
        $method = 'appendAfter'.implode('', array_map('ucfirst', explode('_', $ctype)));
        if(is_callable(array($this, $method)))
        {
            $call[0] = $this;
            $call[1] = $method;
        }
        elseif(is_array($parent_field) 
            && is_callable(array($parent_field[0], ($method = (isset($parent_field[1]) ? $parent_field[1] : 'modelAppendAfter'))))
        ) {
            $call[0] = $parent_field[0];
            $call[1] = $method;
            if(isset($parent_field['field']))
            {
                $parent_field = $parent_field['field'];
                $params['_append_field'] = $parent_field;
            }
        }
        else
        {
            trigger_error('No append after method implementation');
        }

        if($call)
            foreach($data as &$d)
            {
                $result = call_user_func(
                        $call,
                        (is_array($parent_field) || $parent_field == self::APPEND_ARRAY) ? $d : $d[$parent_field],
                        $visitor_id,
                        $params);
                
                if (!empty($params['need_reverse']))
                {
                    $result = array_reverse($result);
                }
                $d[$params['_append_key']] = $result;
            }
            
        return $data;
    }
    
    public function modelAppendAfter($value, $visitor_id, $params)
    {
        $perm_section = (isset($params[self::PERM_SECTION]) ? 
                $params[self::PERM_SECTION] : strtolower(str_replace(Request::MODEL_PREFIX, '', $this->getName())));
        $params = array(
            'session' => array(
                'save' => false
            ),
            'where' => array(
                $params['_append_field'] => $value,
            ),
            self::PERM_SECTION => $perm_section,
        );
        return $this->getBy($params);
    }
    
    /**
     * Merge multidimensional arrays
     * 
     * @param array $array1
     * @param array $array2
     * @return merged array
     */ 
    public final function mergeParamsArrays($array1, $array2, $separator=null)
    {
        foreach($array1 as $key => &$arr)
        {
            if (isset($array2[$key]))
            {
                if (is_array($arr) || is_array($array2[$key]))
                {
                    if(is_null($separator))
                        $arr = array_merge((array)$arr, (array)$array2[$key]);
                    else
                        $arr = $this->appendParamsArrays((array)$arr, (array)$array2[$key], $separator[$key]);
                }
                else
                {
                    if(is_null($separator))
                        $arr = $array2[$key];
                    else
                        $arr .= ' '.$separator[$key].' '.$array2[$key];
                }
            }
        }

        if(is_array($array2) && is_array($array1))
            $array1 += array_diff_assoc($array2, $array1);
        
        return $array1;
    }
    
    public final function appendParamsArrays($array1, $array2, $separator=',')
    {
        return $this->mergeParamsArrays($array1, $array2, $separator);
    }
}
