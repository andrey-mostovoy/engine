<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * BaseDb class that provide basic methods for work with database
 * from models.
 * 
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.31
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass('Base_DB_DbDriver');
Loader::loadClass('Base_DB_Cache');
Loader::loadClass('Base_DB_QueryBuilder');

/**
 * class BaseDb
 * Containing basic methods and properties
 * for work with database
 * {@uses DbDriver}
 * {@uses Cache}
 * 
 * @package		Base
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class BaseDb extends App
{
    /**
	 * bind marker that will be replaced with the match data
	 */
	const BIND_MARKER = '?';
	/**
	 * current database table name
	 * @var string
	 */
	protected $_name = null;
	/**
	 * current query
	 * @var string
	 */
	protected $_query_str = null;
    /**
     * Query stmt options
     * @var array
     */
    private $query_option;
    /**
     * indicator to use option
     * @var type 
     */
    private $use_option = true;
	/**
	 * Query history for debugging.
	 * If DEBUG constant set to true query will be saved in that array
	 * @var array
	 */
	protected static $_queries = array();
	/**
	 * last query operation error text
	 * @var string
	 */
	protected $_error = null;
    /**
     * unescaped chars and strings that contains that chars.
     * need write this chars as part of regular exp
     * @var array
     */
    private static $_unescaped_chars = array(
              'NOW\(\)',
              'VALUES\(.+\)',
              'DATE_FORMAT\(.+\)',
              '[\D\h\s^\-^\+]+[\+\-][\s]\d',
    );
	/**
	 * last inserted id
	 * @var int
	 */
	protected $_insert_id = null;
	/**
	 * numbers of rows affected last query operation except SELECT
	 * @var string
	 */
	protected $_affected_rows = null;
	/**
	 * numbers of rows affected last SELECT query operation
	 * @var int
	 */
	protected $_num_rows = null;
	/**
	 * number of all rows on last SELECT query with LIMIT operation
	 * @var int
	 */
	protected $_total_rows = null;
	/**
	 * select database driver class instance
	 * @var DbDriver
	 */
	protected $_driver = null;
    /**
	 * cache class instance
	 * @var Cache
	 */
    protected $_cache = null;
    /**
     * Flag to use cache if it enable
     * @var bool
     */
    protected $use_cache = true;
    /**
     * Flag of read statement, like Select
     * @var bool 
     */
    private $read_stmt = false;
    /**
     * Array with results from cache
     * @var array 
     */
    private $from_cache = false;
    /**
     * columns in current table
     * @var array
     */ 
    protected $_columns = array();
    /**
     * Indicate execute query or not.
     * Using when need retrieve query str what
     * formated via builder. In other cases use {@link _prepareQuery()}
     * @var bool
     */
    protected $execute_stmt = true;
    /**
     * Paging objects
     * @var Paginator
     */
    private $paging = null;
    /**
     * indicate using paging
     * @var bool
     */
    public $use_paging = true;
    /**
     * class instance
     * @var BaseDb
     */
    private static $instances = null;

	/**
	 * class construct
	 * create connect with db, select database and set charset
	 */
	protected function __construct()
	{
        $this->_driver = DbDriver::getInstance();
        $this->_cache = Cache::getInstance();

        // set data from post request to model datas
//        $this->data( App::controller()->data() );
        
        $this->paging();
        
        $this->_init();
        $this->_run();
//        if($this->use_permission)
//            $this->initPermissions();
        $this->_name = $this->setName();
        
//        //init query builder
//        $this->builder = QueryBuilder::getInstance($this);
//        $this->builder->setDefaultFieldMap(array(
//            'filter' => array(
//                QueryBuilder::PROP_IS_SECTION => true),
//            'append' => array(
//                QueryBuilder::PROP_IS_SECTION  => true,
//                QueryBuilder::PROP_CHECK_EMPTY => true,
//            ))
//       );
	}
    
    /**
     * retrieve model instance
     * 
     * @param string $m - model name
     * @param bool $sp - flag to use current site part dir
     * @return object
     */
    public static function getInstance($m,$sp)
    {
        if (empty($m))
        {
             trigger_error('Wrong or empty model name');
             return null;
        }
        
        if(!isset(self::$instances[$m]) || self::$instances[$m] === null)
        {
            $tm = explode('/', $m);
            $mn = array_pop( $tm );
            Loader::loadModel($m,$sp);

            $mc = Request::MODEL_PREFIX.ucfirst($mn);            
            self::$instances[$m] = new $mc();
        }

        return self::$instances[$m];
    }

    /**
     * Return Paginator object
     * @return Paginator
     */
    public function paging()
    {
        if(is_null($this->paging))
        {
            $this->paging = new Paginator(
                App::lang()->paging()->all(),
                null,
                App::controller()->base_address.'/'.App::request()->getActionName() . App::request()->getQueryParams(array('page',"__a",Controller::FILTER)),
                Config::NUMPAGE_MAIN,
                App::request()->getParam('page', 'main-1', Request::FILTER_STRING),
                App::request()->getParam('ipp', App::request()->getPost('ipp', App::request()->getParam('ipp', null, Request::FILTER_STRING), Request::FILTER_STRING)),
                $this->getName(),
                '',
                Config::PAGING_RANGE_NUM
            );
        }
        return $this->paging;
    }
    
    /**
     * initialize some common features
     */
    protected function _init(){}
    protected function _run(){}
//    protected function initPermissions(){}
    
    /**
     * class destructor
     */
	public function  __destruct()
	{
		$this->_close();
	}

    /**
     * Set name of table model work with.
     * @return string name of table
     */
    abstract protected function setName();

	/**
	 * close connection with database
	 */
	protected final function _close()
	{
		$this->_driver->_close();
	}

	/**
	 * insert data into table
	 *
	 * @param array $data array with pairs key=val. keys must be without '`'. If set array of arrays will be multi insert
	 * @param string $table table name on proccess with
     * @param array $params array with pairs to ON DUPLICATE KEY UPDATE statement
	 * @return boolean if success return true else false
	 */
	protected final function _insertData(array $data, $table=false, $params=null)
	{
		return $this->_setData($data, $table, 'insert', $params);
	}
    
    /**
	 * replace data into table
	 *
	 * @param array $data array with pairs key=val. keys must be without '`'. If set array of arrays will be multi insert
	 * @param string $table table name on proccess with
	 * @return boolean if success return true else false
	 */
    protected function _replaceData(array $data, $table=false)
	{
		return $this->_setData($data, $table, 'replace');
	}
    
    /**
	 * set data into db table
	 *
	 * @param array $data array with pairs key=val. keys must be without '`'. If set array of arrays will be multi insert
	 * @param string $table table name on proccess with
     * @param string $method name of method of db driver object, i.e. _update or _insert
     * @param array $params array with pairs to ON DUPLICATE KEY UPDATE statement
	 * @return boolean if success return true else false
	 */
    private function _setData(array $data, $table=false, $method='insert', $params=null)
	{
		$this->_formTableName($table);
        
		$fields = array();
		$values = array();

        $this->filterColumns($data, $table);
        
        $on_dku = '';
        // on duplicate field update statement
        if(!empty($params))
        {
            $on_dku_fields = array();
            foreach($params as $key => $val)
            {
                $on_dku_fields[$this->_driver->_escapeIdentifiers($key)] = $this->_escape($val);
            }
            $on_dku = $this->_driver->_onDuplicateKey($on_dku_fields);
        }
        
        reset($data);
        //check for multiInsert
        if(is_array($data) && is_array(current($data)))
        {
            $first = key($data);
            foreach($data as $k=>&$d)
            {
                foreach($d as $key => &$val)
                {
                    if($k == $first)
                        $fields[] = $this->_driver->_escapeIdentifiers($key);
                    $values[$k][] = $this->_escape($val);
                }
            }
            $this->_query(
                    $this->_driver->{'_multi'.ucfirst($method)}($table, $fields, $values)
                    . $on_dku
            );
        }
        else
        {
            foreach($data as $key => $val)
            {
                $fields[] = $this->_driver->_escapeIdentifiers($key);
                $values[] = $this->_escape($val);
            }
            
            $this->_query( 
                    $this->_driver->{'_'.$method}($table, $fields, $values) 
                    . $on_dku
            );
        }
        
        $this->_cache->setResetFlag(array($table));
        
        return $this->_driver->_result;
	}

	/**
	 * update data in table
	 *
	 * @param array $data array with pairs key=val of field and his value.
	 * @param mixed $where sql where clause. can be array with key=val or string with conditions without 'WHERE'
	 * @param strring $table [optional] table name on proccess with.
	 * @return boolean on success return true otherwise false
	 */
	protected final function _updateData(array $data, $where, $table=false)
	{
		$this->_formTableName($table);
        $this->filterColumns($data, $table);
        
		if (empty($where))
		{
			$this->_error = 'Attempt to update all data on table \''.$table.'\'';
			throw new SqlException( $this->_error );
		}

		$fields = array();
		foreach($data as $key => $val)
		{
			$fields[$this->_driver->_escapeIdentifiers($key)] = $this->insKeyForMath($key, $val).$this->_escape($val);
		}

        $this->_cache->setResetFlag(array($table));
        
		$this->_query( $this->_driver->_update($table, $fields, $this->_formatWhereString($where)) );
		return $this->_driver->_result;
	}

	/**
	 * delete data from table
	 *
	 * @param mixed $where sql where clause. can be array with key=val or string with conditions without 'WHERE'
	 * @param string $table table name on proccess with
	 * @return boolean on success return true otherwise false
	 */
	protected final function _deleteData($where, $table=false)
	{
		$this->_formTableName($table);

		if (empty($where))
		{
			$this->_error = 'Attempt to delete all data from table \''.$table.'\'';
			throw new SqlException( $this->_error );
		}

        $this->_cache->setResetFlag(array($table));
        
		$this->_query( $this->_driver->_delete($table, $this->_formatWhereString($where)) );
		return $this->_driver->_result;
	}
    
    /**
	 * delete data from table. Use truncate
	 *
	 * @param string $table table name on proccess with
	 * @return boolean on success return true otherwise false
	 */
    protected function _truncateTable($table=false)
    {
        $this->_formTableName($table);

        $this->_cache->setResetFlag(array($table));
        
		$this->_query( $this->_driver->_truncate($table) );
		return $this->_driver->_result;
    }

	/**
	 * Return result array or false on error
	 * @param string|array	$query	if string - query string. If empty will be generated select all element from current table
     *                              if array - array of arrays with query and its params array. For union select
	 * @param array         $params	array of arrays:
	 *							where - array with SQL field=value pairs or string with conditions without 'WHERE'
	 *							group - array for GROUP BY with values like: 'field1 DESC', 'field2'
	 *							having - array with SQL field=value pairs or string with conditions without 'HAVING'
	 *							order - array for ORDER BY with values like: 'field1 DESC', 'field2'
	 *							limit - array with limit numbers or with one number for limit without paging
     *                          no_last - bool flag if no need retrieve last no empty result page
	 *							Also if present numeric keys in that array it will be placed as a binds data
	 * @return boolean|array return boolean on error or array on success
	 */
	protected final function _selectData($query='', $params=null)
	{
        if(is_array($query))
        {
            list($query, $param) = $this->_formUnionSelect($query);
            $params = array_merge($params, $param);
        }
        
        $this->read_stmt = true;
        $this->from_cache = false;
                
        if(empty($query))
        {
            $query = $this->_driver->_select($this->_name);
        }
		$this->_query($query, $params);

        if($this->from_cache)
        {
            return $this->from_cache;
        }
        
		$result_array = $this->_fetch();

		if ( empty($result_array) 
            && $this->use_paging
            && !isset($params['no_last']) 
            && !empty($params['limit']) 
            && $params['limit'][0] != 0
        ) {
			$this->paging()->setCurrentPage( $this->paging()->getLastNoEmptyResultPage() );
			$params['limit'] = $this->paging()->getLimitArray();
			$this->_query($query, $params);
			$result_array = $this->_fetch();
		}
        
        $this->read_stmt = false;
        
        $this->_cache->setQueryResult($this->_query_str, array(
            'res'       => $result_array,
            'total_rows'=> $this->_total_rows,
        ), Defines::CACHE_TTL);
                
		return $result_array;
	}

    private function _formUnionSelect($stmts)
    {
        $limit = false;
        foreach($stmts as $k=>&$stmt)
        {
            if(!empty($stmt['params']['limit']))
            {
                if(empty($limit))
                    $limit = $stmt['params']['limit'];
                
                unset($stmts[$k]['params']['limit']);
            }
            
            $this->_prepareQuery($stmt['query'], $stmt['params']);
            $stmt = $this->_query_str;
        }
        unset($stmt);
        return array($this->_driver->_selectUnion($stmts), array('limit'=>$limit));
    }
    
    protected final function setQueryOption($option)
    {
        $this->query_option[$option] = $option;
    }
    protected final function clearQueryOption($option=null)
    {
        if(!is_null($option))
        {
            unset($this->query_option[$option]);
        }
        else
        {
            $this->query_option=null;
        }
    }
	/**
	 * execute query. On error generate user level warning.
	 *
	 * @param string     $query query string
	 * @param array     $params array of arrays:
	 *			where - array with SQL field=value pairs or string with conditions without 'WHERE'
	 *			group - array for GROUP BY with values like: 'field1 DESC', 'field2'
	 *			having - array with SQL field=value pairs or string with conditions without 'HAVING'
	 *			order - array for ORDER BY with values like: 'field1 DESC', 'field2'
	 *			limit - array with limit numbers or with one number for limit without paging
	 *			Also if present numeric keys in that array it will be placed as a binds data
	 * @return mixed On SELECT query return resource.
	 *				 On others return boolean. True on success query and false on some error
	 */
	protected final function _query($query='', $params=null)
	{
		$this->_prepareQuery($query, $params);

		if ( Config::DEBUG )
			self::$_queries[] = $this->_query_str;

        if($this->use_cache && $this->read_stmt)
        {
            $matches = array();
            preg_match_all('/(FROM|JOIN)+\s[`]*([\w]+)[`]*/i', $this->_query_str, $matches);

            if(!empty($matches[2])
                && ($this->from_cache = $this->_cache->getQueryResult($this->_query_str, $matches[2]))
                && !empty($this->from_cache['res'])
            ) {
                $this->_total_rows = $this->from_cache['total_rows'];
                if ( !empty($params['limit']) && $this->use_paging)
                {
                    $this->paging()->setTotalItems($this->_total_rows);
                    $this->paging()->paginate();
                }
                return $this->from_cache = $this->from_cache['res'];
            }
            else
            {
                $this->from_cache = false;
            }
        }

        $this->_driver->_query_str = $this->_query_str;

        $this->_driver->_result = $this->_driver->_execute();

        if ( !$this->_checkAndSetError() )
        {
            if ( !is_bool( $this->_driver->_result ) )
                $this->_num_rows = $this->_driver->_numRows();
            else
            {
                $this->_insert_id = $this->_driver->_insertId();
                $this->_affected_rows = $this->_driver->_affectedRows();
            }
            $this->_total_rows = $this->_driver->_foundRows();

            if ( !empty($params['limit']) && $this->use_paging)
            {
                $this->paging()->setTotalItems($this->_total_rows);
                $this->paging()->paginate();
            }
        }

        return $this->_driver->_result;
	}

    /**
	 * Prepare query string.
	 *
	 * @param string    $query query string
	 * @param array     $params array of arrays:
	 *			where - array with SQL field=value pairs or string with conditions without 'WHERE'
	 *			group - array for GROUP BY with values like: 'field1 DESC', 'field2'
	 *			having - array with SQL field=value pairs or string with conditions without 'HAVING'
	 *			order - array for ORDER BY with values like: 'field1 DESC', 'field2'
	 *			limit - array with limit numbers or with one number for limit without paging
	 *			Also if present numeric keys in that array it will be placed as a binds data
	 * @return mixed On SELECT query return resource.
	 *				 On others return boolean. True on success query and false on some error
	 */
    protected final function _prepareQuery($query='', $params=null)
    {
        if ( !empty($query) )
			$this->_query_str = trim($query);

		if (!empty($params['where']) )
		{
			$params['where'] = $this->_formatWhereString($params['where']);
			$this->_query_str .= (!empty($params['where']) ? " \n".(strpos($params['where'], 'WHERE')===false ? ' WHERE ' : ' AND ').$params['where'] : '');
		}
		if (!empty($params['group']))
		{
			$this->_query_str .= " \n".' GROUP BY ' . implode( ', ', $params['group'] );
		}
		if (!empty($params['having']))
		{
			$params['having'] = $this->_formatWhereString($params['having']);
			$this->_query_str .= (!empty($params['having']) ? " \n".(strpos($params['having'], 'HAVING')===false ? ' HAVING ' : ' AND ').$params['having'] : '');
		}
		if (!empty($params['order']))
		{
			$this->_query_str .= " \n".' ORDER BY ' . implode( ', ', $params['order'] );
		}
		if (!empty($params['limit']) && $params['limit'] !== false 
            && $params['limit'] !== 'all' && $params['limit'][0] !== 'all'
        ) {
            $params['limit'] = (array) $params['limit'];
            if($params['limit'][0] < 0)
                $params['limit'][0] = 0;
			if (!empty($params['limit'][1]))
            {
                $this->setQueryOption('SQL_CALC_FOUND_ROWS');
                $this->addOption();
                $this->clearQueryOption();
            }
			$this->_query_str .= " \n".' LIMIT '.$params['limit'][0].(isset($params['limit'][1])?', '.$params['limit'][1]:'');
		}

        $this->addOption();
        
		// it show if we have binds in array
		if ( isset($params[0]) )
			$this->_query_str = $this->_compileBinds($this->_query_str, $params);
    }
    
    private function addOption()
    {
        if($this->use_option && !empty($this->query_option))
        {
            $this->_query_str = preg_replace('/^(\w+)/', '\\1 '.implode(' ', $this->query_option), $this->_query_str);
        }
    }
	/**
	 * check for error and on error case generate user level warning
	 *
	 * @return boolean return true if error excist otherwise false
	 */
	private function _checkAndSetError()
	{
		if ( $this->_driver->_errorNumber() != NULL )
		{
			$this->_error = $this->_driver->_errorNumber() . ': ' . $this->_driver->_errorMessage().'. <b>Query: '.$this->_query_str.'</b>';
			trigger_error($this->_error);
			return true;
		}
		return false;
	}

    /**
     * Call db procedure
     * @param string $name procedure name to call
     * @param array $input [optional] input arguments. Notice: they come first in procedure declaration
     * @param array $output [optional] output arguments. Notice: they come withou '@' character.
     * And return values also will not have '@'. Come last in procedure declaration
     * @return bool|array 
     */
    protected final function call($name, array $input=array(), array $output=array())
    {
        $strs = $this->_driver->_call($name, $input, $output);
        
        $this->_query($strs[0]);
        if(!empty($strs[1]))
        {
            $result = null;
            $res = $this->_selectData($strs[1]);
            if(!empty($res[0]))
            {
                foreach($res[0] as $k => &$r)
                {
                    $result[ltrim($k,'@')] = $r;
                }
            }
            return $result;
        }
        return $this->_driver->_result;
    }
    
	/**
	 * Return one value from table
	 *
	 * @param string $field one field from table
	 * @param mixed $where sql where cause paire. can be array with key=val or string with conditions without 'WHERE'
	 * @param string $table table name on proccess with
     * @param array $order  [optional] array with fields for ORDER BY. ASC order by default. If need DESC add it to field name and separate with space
	 * @return mixed on success return string otherwise boolean false
	 */
	protected final function _fetchCell($field, $where, $table=false, $order=null)
	{
		$this->_formTableName($table);

		$this->_query(
                'SELECT '.$field.' as cell FROM '.$table,
                array('where'=>$where, 'order'=>$order, 'limit'=>array(1))
        );
        return $this->_driver->_fetchOne();
	}

	/**
	 * Return associate array of query result data
	 * @return mixed return boolean on error(false) or array of data on success or null if no result
	 */
	protected final function _fetch()
	{
		if ( !is_bool($this->_driver->_result) )
		{
			return $this->_driver->_fetch();
		}
		else return $this->_driver->_result;
	}

	/**
	 * return table name with '`' symbols
	 * @param mixed $r_table by reference. If false took table name property from child model
	 */
	private function _formTableName(&$r_table)
	{
		$r_table = trim( !empty($r_table) ? $r_table : $this->_name );
		strpos($r_table, '`')!==false or $r_table='`'.str_replace('.', '`.`', $r_table).'`';
	}

	/**
	 * Generate where string clause
	 *
	 * @param mixed $where array with key-value pairse or string with conditions without 'WHERE'
	 * @return string
	 */
	private function _formatWhereString($where, $mprefix=null)
	{
		if ( ! is_array($where))
		{
			$dest = array($where);
		}
		else
		{
			$dest = array();
			foreach ($where as $key => $val)
			{
                $prefix = (count($dest) == 0) ? '' : ' '.(is_null($mprefix) ? 'AND' : strtoupper($mprefix)).' ';
                
                if(!is_numeric($key) && $key == 'or')
                {
                    $dest[] = $prefix.'('.$this->_formatWhereString($val, $key).')';
                    continue;
                }
				
                $suffix = '';
				if ($val !== '')
				{
                    if(!is_array($val) 
                        && preg_match("/([\%\d\w\.\`]+)\s(or)\s([\%\d\w\.\`]+)/i", $val, $match)
                        && !empty($match)
                    ) {
                        unset($match[0]);
                        
                        $val = '';
                        foreach($match as &$m)
                        {
                            if(strtolower($m) == 'or')
                            {
                                $val .= " $m ";
                            }
                            else
                            {
                                if ( ! $this->_isHasOperator($key))
                                {
                                    if(is_numeric($key))
                                        $key = '';
                                    else
                                        $key .= ' =';
                                }
                                
                                $val .= $key.' '.$this->_escape($m,$this->_isHasLikeOperator($key));
                            }
                        }
                        $key = '';
                        $prefix .= '(';
                        $suffix = ')';
                    }
                    else
                    {
                        if ( ! $this->_isHasOperator($key))
                        {
                            if(is_numeric($key))
                                $key = '';
                            else
                                $key .= ' =';
                        }
                        
                        $val = ' '.$this->_escape($val,$this->_isHasLikeOperator($key));
                    }
				}

				$dest[] = $prefix.$key.$val.$suffix;
			}
		}
		return (!empty($dest) ? implode(' ', $dest) : '');
	}

    /**
     * Form 'where' sql statement
     * @param array $params
     * @return array
     */
    public final function formWhere($params)
    {
        if(!empty($params))
        {
//            if(is_array($params))
//            {
//                return array('where'=>$this->_formatWhereString($params));
//            }
            return array("where" => $params);
        }
    }
    
    /**
     * retrive total row number after last select query
     * @return int
     */
    public final function getQueryTotalRows()
    {
        return $this->_total_rows ? $this->_total_rows : false;
    }

	/**
	 * "Smart" Escape String
	 *
	 * Escapes data based on type
	 * Sets boolean and null types
	 *
	 * @param	string $str string to escape
     * @param	bool $like indicate that string used in like
	 * @return	mixed
	 */
	protected function _escape($str, $like=false)
	{
		if (is_array($str))
		{
			$str = '('.implode(', ', $this->_driver->_escapeStr($str, $like)).')';
		}
        elseif ( !preg_match('/^'.  implode('|', self::$_unescaped_chars).'$/', $str) )
        {
            if (is_string($str))
            {
                $str = '\''.$this->_driver->_escapeStr($str, $like).'\'';
            }
            elseif (is_bool($str))
            {
                $str = ($str === false) ? 0 : 1;
            }
            elseif (is_null($str))
            {
                $str = 'NULL';
            }
        }

		return $str;
	}
    /**
	 * Tests whether the string has an SQL operator like
	 *
	 * @param	string $str
	 * @return	bool
	 */
	private function _isHasLikeOperator($str)
	{
		if ( false === strpos($str, 'like') )
		{
			return false;
		}
		return true;
	}
	/**
	 * Tests whether the string has an SQL operator
	 *
	 * @param	string $str
	 * @return	bool
	 */
	private function _isHasOperator($str)
	{
		$str = trim($str);
		if ( ! preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str))
		{
			return false;
		}
		return true;
	}
	/**
	 * Tests whether the string has an SQL operator
	 *
	 * @param	string $str
	 * @return	bool
	 */
	private function _isHasOr($str)
	{
		$str = trim($str);
		if ( ! preg_match("/\s(or)\s/i", $str))
		{
			return false;
		}
		return true;
	}
    /**
     * Test string id data update for math operation for update like
     * 'value'=>' - 2'. This will transform to `value` = `value` - 2 in sql.
     * @param string $str
     * @return bool
     */
    private function isHasMath($str)
    {
        if(preg_match('/^'.self::$_unescaped_chars[3].'$/', $str))
        {
            return true;
        }
        return false;
    }
    
    private function insKeyForMath($key, $val)
    {
        if($this->isHasMath($val))
        {
            return $this->_driver->_escapeIdentifiers($key);
        }
        return '';
    }

	/**
	 * Compile Bindings
	 *
	 * @param	string $sql	the sql statement
	 * @param	array $binds	an array of bind data
	 * @return	string
	 */
	private function _compileBinds($sql, $binds)
	{
		if (strpos($sql, self::BIND_MARKER) === FALSE)
		{
			return $sql;
		}

		if ( ! is_array($binds))
		{
			$binds = array($binds);
		}

		//delete all string keys from array
		//it can be limit, where or other keys
		foreach($binds as $k=>$v)
			if (is_string($k))
				unset($binds[$k]);
		// Get the sql segments around the bind markers
		$segments = explode(self::BIND_MARKER, $sql);

		// The count of bind should be 1 less then the count of segments
		// If there are more bind arguments trim it down
		if (count($binds) >= count($segments)) {
			$binds = array_slice($binds, 0, count($segments)-1);
		}

		// Construct the binded query
		$result = $segments[0];
		$i = 0;
		foreach ($binds as $bind)
		{
			$result .= $this->_escape($bind);
			$result .= $segments[++$i];
		}

		return $result;
	}

    /**
     * complite given array with new parameters for work with db methods
     * @param array $r_params array by referer to complite with parameters
     * @param mixed $where  [optional] array with key=>val pairse of where case or string with conditions without 'WHERE'
     * @param mixed $limit  [optional] can be numeric if need create pagging or can be array with one element for select with limit without pagging
     * @param array $order  [optional] array with fields for ORDER BY. ASC order by default. If need DESC add it to field name and separate with space
     * @param array $group  [optional] array with fields for GROUP BY. ASC order by default. If need DESC add it to field name and separate with space
     * @param mixed $having [optional] array with key=>val pairse of having case or string with conditions without 'HAVING'
     */
    protected final function prepareParams($where=null, $limit=null, $order=null, $group=null, $having=null)
    {
        $params = array();
        if (!empty($where))
			$params['where'] = $where;
        if (!empty($limit))
			$params['limit'] = (is_numeric($limit) ? $this->paging()->getLimitArray($limit) : $limit);
        if (!empty($order) && is_array($order))
			$params['order'] = $order;
        if (!empty($group) && is_array($group))
			$params['group'] = $group;
        if (!empty($having))
			$params['having'] = $having;
        return $params;
    }

    /***********************************************
     *         General Model Functions             *
     **********************************************/

    /**
     * General method to select data from current model table
     * @param mixed $where  [optional] array with key=>val pairse of where case or string with conditions without 'WHERE'
     * @param mixed $limit  [optional] can be numeric if need create pagging or can be array with one element for select with limit without pagging
     * @param array $order  [optional] array with fields for ORDER BY. ASC order by default. If need DESC add it to field name and separate with space
     * @param array $group  [optional] array with fields for GROUP BY. ASC order by default. If need DESC add it to field name and separate with space
     * @param mixed $having [optional] array with key=>val pairse of having case or string with conditions without 'HAVING'
     * @return mixed return array with results or false on errors
     */
	public function get($where=null, $limit=null, $order=null, $group=null, $having=null)
	{
		$params = $this->prepareParams($where, $limit, $order, $group, $having);
		return $this->_selectData('', $params);
	}

    /**
     * General method to get one row from select result
     * @param array $where  array with key=>val pairse of where case or string with conditions without 'WHERE'
     * @param array $order  [optional] array with fields for ORDER BY. ASC order by default. If need DESC add it to field name and separate with space
     * @param array $group  [optional] array with fields for GROUP BY. ASC order by default. If need DESC add it to field name and separate with space
     * @param array $having [optional] array with key=>val pairse of having case or string with conditions without 'HAVING'
     * @return mixed return array with row results or false on errors
     */
    public function getRow($where, $order=null, $group=null, $having=null, $limit = array(1))
    {
        if (!empty($where))
        {
            $params = $this->prepareParams($where, $limit, $order, $group, $having);
           
            $res = $this->_selectData('', $params);
            if (!empty($res))
                return $res[0];
            return false;
        }
    }

    /**
     * General method to get one value from table
     * @param string    $field  field name
     * @param array     $where  array with key=>val pairse of where case or string with conditions without 'WHERE'
     * @param string    $table table name on proccess with
     * @param array     $order  [optional] array with fields for ORDER BY. ASC order by default. If need DESC add it to field name and separate with space
     * @return string return field value
     */
    public function getOne($field, $where, $table=false, $order=null)
    {
        if (!empty($field) && !empty($where))
        {
            return $this->_fetchCell($field, $where, $table, $order);
        }
        return false;
    }

    /**
     * Return count of elements from table depend on $where
     * @param array     $where  array with key=>val pairse of where case or string with conditions without 'WHERE'
     * @param string    $table table name on proccess with
     * @return string elements count
     */
    public function getNum($where=null, $table=false)
    {
        $query = 'SELECT count(*) as c FROM '.$this->_name;
        $params = $this->prepareParams($where);
        $res = $this->_selectData($query, $params);
        return $res[0]['c'];
    }

    /**
     * General method to add new data to table
     * @param array $data array with new data to insert
     * @return bool return true on success or false on errors
     */
    public function add($data)
    {
        if (!empty($data) && is_array($data))
        {
            return $this->_insertData($data);
        }
        return false;
    }

    /**
     * General method to update data in table
     * @param array $data   array with new data to update
     * @param mixed $where  array with key=>val pairse of where case or string with conditions without 'WHERE'
     * @return bool return true on success or false on errors
     */
    public function update($data, $where)
    {
        if (!empty($data) && !empty($where) && is_array($data))
        {
            return $this->_updateData($data, $where);
        }
        return false;
    }

    /**
     * General method to delete data from table
     * @param mixed $where  array with key=>val pairse of where case or string with conditions without 'WHERE'
     * @return bool return true on success or false on errors
     */
    public function delete($where)
    {
        if (!empty($where))
        {
            return $this->_deleteData($where);
        }
        return false;
    }
    
    /**
     * Filter array. Only fields from $this->_columns will be presented
     * {@uses filterDataByColumns}
     * @param array $data
     */ 
    protected final function filterColumns(&$data, $table=false)
    {
        //get list of existed columns if needed
        if (empty($this->_columns) || $this->_name != $table)
        {
            $this->_columns = $this->getColumns($table);
        }
        
        //perform filtration
        if (empty($data) || !is_array($data))
        {
            $data = array();
            return;
        }
        
        $this->filterDataByColumns($data);
    }
    /**
     * Filter fields in data array for according to table columns
     * @param type $data 
     */
    private function filterDataByColumns(&$data)
    {
        foreach($data as $k => &$d)
        {
            if(is_array($d))
            {
                if(!is_numeric($k) && !in_array($k, $this->_columns))
                {
                    unset($data[$k]);
                }
                else
                {
                    $this->filterDataByColumns($d);
                }
            }
            else
            {
                if(!in_array($k, $this->_columns))
                {
                    unset($data[$k]);
                }
            }
        }
    }
    
    /**
     * Get column list from table
     * 
     * @param string $table
     * @return array|null
     */ 
    protected final function getColumns($table = false)
    {
        $this->_formTableName($table);
     
        $this->use_option = false;
        
        $result = $this->_selectData('SHOW COLUMNS FROM ' . $table);
        
        $this->use_option = true;
        
        if (!$result) return null;
        
        $columns = array();
        foreach ($result as $column)
        {
            $columns[] = $column['Field'];
        }
        
        return $columns;
    }
    /**
     * Get model class name
     * @return string
     */
    public final function getName()
    {
        return get_class($this);
    }
    
    public final function getQueryCount()
    {
        return count(self::$_queries);
    }
    
    public static final function getQueries()
    {
        return self::$_queries;
    }
}

/* End of file BaseDb.php */
/* Location: ./class/Base/DB/BaseDb.php */
?>