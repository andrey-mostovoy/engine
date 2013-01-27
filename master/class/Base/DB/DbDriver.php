<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * DbDriver class that provides basic methods for work with
 * selected database type (mysql, odbc, etc..)
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

Loader::loadClass('Base_DB_drivers_'.Config::DATABASE_DBDRIVER);

/**
 * class DbDriver
 * containing basic methods and properties
 * for work with selected database type
 *
 * @package		Base
 * @subpackage	Drivers
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class DbDriver
{
	/**
	 * current connect identifier
	 * @var resource
	 */
	protected $_conn_id = null;

	/**
	 * current query result. Can be bool on all "write" and errors on "read" operations and can be array on "read" operations
	 * @var mixed
	 */
	public $_result = null;

    /**
     * database configuration information
     * @var array
     */
    protected $_config = array();

	/**
	 * current Database driver type
	 * @var string
	 */
	protected $_db_driver = '';

	/**
	 * current query
	 * @var string
	 */
	public $_query_str = null;

	/**
	 * Database charset
	 * @var string
	 */
	protected $_char_set = 'utf8';

	/**
	 * Database collation
	 * @var string
	 */
	protected $_db_collat = 'utf8_general_ci';

	/**
	 * No escaped things
	 * @var array
	 */
	protected $_reserved_identifiers = array('*'); // Identifiers that should NOT be escaped

	/**
	 * child class instance
	 * @var object
	 */
	private static $_instance = null;

	/**
	 * create database connection, select project database and set charset
	 * @throws SqlException
	 */
	private function __construct()
	{
        $this->_config = array(
            'server'    => Config::DATABASE_SERVER,
            'username'  => Config::DATABASE_USERNAME,
            'password'  => Config::DATABASE_PASSWORD,
            'dbname'    => Config::DATABASE_DBNAME
        );
        
		$this->_conn_id = $this->_dbConnect();

		if ( !$this->_conn_id )
			throw new SqlException( 'Error connecting to database: '.$this->_errorMessage() );

		if ( !$this->_dbSelect() )
			throw new SqlException ( 'Could not select database: '.$this->_errorMessage() );

		if ( !$this->_dbSetCharset($this->_char_set, $this->_db_collat) )
            trigger_error('Can\'t set charset');
	}

	/**
	 * retrieve child class object
	 *
	 * @return object
	 */
	public static final function getInstance()
	{
        $class = Config::DATABASE_DBDRIVER.'DbDriver'; 
		self::$_instance === null and self::$_instance = new $class(); 
		return self::$_instance;
	}

	/**
	 * close connection with database
	 */
	public final function _close()
	{
		if (is_resource($this->_conn_id) || is_object($this->_conn_id))
		{
			$this->_dbClose($this->_conn_id);
		}
		$this->_conn_id = NULL;
	}

	/**
	 * Non-persistent database connection
	 * @return	resource
	 */
	abstract public function _dbConnect();

	/**
	 * Persistent database connection
	 * @return	resource
	 */
	abstract public function _dbPconnect();

	/**
	 * Reconnect
	 *
	 * Keep / reestablish the db connection if no queries have been
	 * sent for a length of time exceeding the server's idle timeout
	 */
	abstract public function _reconnect();

	/**
	 * Select the database
	 * @return	resource
	 */
	abstract public function _dbSelect();

	/**
	 * Set client character set
	 *
	 * @param	string	$charset
	 * @param	string	$collation
	 * @return	resource
	 */
	abstract public function _dbSetCharset($charset, $collation);

	/**
	 * Close DB Connection
	 *
	 * @param resource $conn_id connection id
	 */
	abstract public function _dbClose($conn_id);

	/**
	 * Insert statement
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	$table	the table name
	 * @param	array	$keys	the insert keys
	 * @param	array	$values	the insert values
	 * @return	string
	 */
	abstract public function _insert($table, $keys, $values);

	/**
	 * Update statement
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	$table      the table name
	 * @param	array	$values     the update data
	 * @param	string	$where      the where conditions
	 * @param	array	$orderby	the orderby conditions
	 * @param	string	$limit      the limit
	 * @return	string
	 */
	abstract public function _update($table, $values, $where, $orderby = array(), $limit = FALSE);

	/**
	 * Delete statement
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param string $table table name
	 * @param string $where	where condition
	 * @return string
	 */
	abstract public function _delete($table, $where);

	/**
	 * Execute the query
	 * @return resource
	 */
	abstract public function _execute();
    /**
     * Escape value string to like condition
     * @param string $str 
     * @return string 
     */
    public final function escapeValToLike($str)
    {
        // escape LIKE condition wildcards
        if (!empty($str))
		{
            $b = substr($str,0,1);
            if(strlen($str) > 1)
                $e = substr($str,-1);
            else
                $e='';
			$str = $b . str_replace(array('%', '_'), array('\\%', '\\_'), substr($str,1,-1)) . $e;
		}
        return $str;
    }
	/**
	 * Escape String
	 *
	 * @param	mixed	$str	array given if we escape values to like operation
	 *							string in other cases
	 * @param	bool	$like	whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	abstract public function _escapeStr($str, $like = false);

	/**
	 * Escape the SQL Identifiers
	 * This function escapes column and table names
	 *
	 * @param	string $item string what need to escape
	 * @return	string
	 */
	abstract public function _escapeIdentifiers($item);

	/**
	 * The error message string
	 * @return	string
	 */
	abstract public function _errorMessage();

	/**
	 * The error message number
	 * @return	integer
	 */
	abstract public function _errorNumber();

	/**
	 * Affected Rows
	 * @return	integer
	 */
	abstract public function _affectedRows();

	/**
	 * Insert ID
	 * @return	integer
	 */
	abstract public function _insertId();

	/****************************************
	 *			WORK WITH RESULT			*
	 ****************************************/

	/**
	 * Number of rows in the result set
	 * @return	integer
	 */
	abstract public function _numRows();

	/**
	 * Number of fields in the result set
	 * @return	integer
	 */
	abstract public function _numFields();

	/**
	 * Return total number of rows even with LIMIT query
	 * @return int
	 */
	abstract public function _foundRows();

	/**
	 * Fetch Field Names
	 * Generates an array of column names
	 *
	 * @return	array
	 */
	abstract public function _listFields();

	/**
	 * Generates an array of objects containing field meta-data
	 *
	 * @return	array
	 */
	abstract public function _fieldData();
	/**
	 * Free the result
	 */
	abstract public function _freeResult();
    /**
	 * Returns the result set as an array
	 *
	 * @return	array array of data
	 */
    abstract public function _fetch();
    /**
     * Return single result from 0 row and 0 field.
     * Other words return one cell
     * @return string
     */
    abstract public function _fetchOne();
	/**
	 * Returns the result set as an array
	 *
	 * @return	array associative array
	 */
	abstract public function _fetchAssoc();

	/**
	 * Returns the result set as an object
	 *
	 * @return	object
	 */
	abstract public function _fetchObject();
    
    /**
     * Return strings sql statements to call db procedure and get results
     * @param string $name procedure name to call
     * @param array $input [optional] input arguments. Notice: they come first in procedure declaration
     * @param array $output [optional] output arguments. Notice: they come withou '@' character.
     * Come last in procedure declaration
     * @return array in 0 key contains call procedure statement, in 1 key - select data statement
     */
    abstract public function _call($name, array $input=array(), array $output=array());
}

/* End of file DbDriver.php */
/* Location: ./class/Base/DB/DbDriver.php */
?>