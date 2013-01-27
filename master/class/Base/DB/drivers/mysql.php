<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version     Version 1.1
 * @since		Version 1.2
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MySQL Database Adapter Class
 * 
 * Provides connecting, reconecting, close methods
 * and formating insert, update, delete statement and
 * execute functionality. Also can parse sql result
 * 
 * @package		Base
 * @subpackage	Drivers
 * @category	Database
 * @author		amostovoy
 */
class mysqlDbDriver extends DbDriver
{
	/**
	 * Database driver type
	 * @var string
	 */
	protected $_db_driver = 'mysql';

	/**
	 * The character used for escaping
	 * @var string
	 */
	private $_escape_char = '`';

	/**
	 * Non-persistent database connection
	 * @return	resource
	 */
	public final function _dbConnect()
	{
		return mysql_connect(	$this->_config['server'],
								$this->_config['username'],
								$this->_config['password']
							  );
	}

	/**
	 * Persistent database connection
	 * @return	resource
	 */
	public final function _dbPconnect()
	{
		return mysql_pconnect($this->_config['server'], $this->_config['username'], $this->_config['password']);
	}

	/**
	 * Reconnect
	 *
	 * Keep / reestablish the db connection if no queries have been
	 * sent for a length of time exceeding the server's idle timeout
	 */
	public final function _reconnect()
	{
		if (mysql_ping($this->_conn_id) === FALSE)
		{
			$this->_conn_id = FALSE;
		}
	}

	/**
	 * Select the database
	 * @return	resource
	 */
	public final function _dbSelect()
	{
		return mysql_select_db($this->_config['dbname'], $this->_conn_id);
	}

	/**
	 * Set client character set
	 *
	 * @param	string	$charset
	 * @param	string	$collation
	 * @return	resource
	 */
	public final function _dbSetCharset($charset, $collation)
	{
		return @mysql_query("SET NAMES '".$this->_escapeStr($charset)."' COLLATE '".$this->_escapeStr($collation)."'", $this->_conn_id);
	}

	/**
	 * Close DB Connection
	 *
	 * @param resource $conn_id connection id
	 */
	public final function _dbClose($conn_id)
	{
		@mysql_close($conn_id);
	}

    /**
     * Select statement
     * Generates a platform-specific select string from the supplied data
     *
     * @param	string	$table	the table name
	 * @return	string
     */
    public final function _select($table)
    {
        return 'SELECT * FROM '.$table;
    }
    
    public final function _selectUnion($stmts)
    {
        return 'SELECT t.* FROM ('."\r\n".'('.implode(')'."\r\n\r\n".' UNION ALL '."\r\n\r\n".'(', $stmts).')'."\r\n".') as t';
    }

	/**
	 * Insert statement
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	$table	the table name
	 * @param	array	$keys	the insert keys
	 * @param	array	$values	the insert values
	 * @return	string
	 */
	public final function _insert($table, $keys, $values)
	{
		return 'INSERT ' . $this->_setData($table, $keys, $values);
	}
    
    /**
	 * Replace statement
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	$table	the table name
	 * @param	array	$keys	the insert keys
	 * @param	array	$values	the insert values
	 * @return	string
	 */
	public final function _replace($table, $keys, $values)
	{
		return 'REPLACE ' . $this->_setData($table, $keys, $values);
	}
    
    /**
	 * Set Data common statement
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	$table	the table name
	 * @param	array	$keys	the insert keys
	 * @param	array	$values	the insert values
	 * @return	string
	 */
    private function _setData($table, $keys, $values)
    {
        return 'INTO ' . $table . ' (' . implode(', ', $keys). ') VALUES ('.implode(', ', $values).')';
    }

    /**
     * Generate ON DUPLICATE KEY UPDATE statement
     */
    public final function _onDuplicateKey($values)
    {
        foreach($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}
        
        return 'ON DUPLICATE KEY UPDATE '.implode(', ', $valstr);
    }
    
    /**
	 * Multi Insert statement
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	$table	the table name
	 * @param	array	$keys	the insert keys
	 * @param	array	$values	the insert values
	 * @return	string
	 */
	public final function _multiInsert($table, $keys, $values)
	{
		return 'INSERT ' . $this->_multiSetData($table, $keys, $values);
	}
    
    /**
	 * Multi Replace statement
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	$table	the table name
	 * @param	array	$keys	the insert keys
	 * @param	array	$values	the insert values
	 * @return	string
	 */
	public final function _multiReplace($table, $keys, $values)
	{
		return 'REPLACE ' . $this->_multiSetData($table, $keys, $values);
	}
    
    /**
	 * Multi Set Data common statement
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	$table	the table name
	 * @param	array	$keys	the insert keys
	 * @param	array	$values	the insert values
	 * @return	string
	 */
	private function _multiSetData($table, $keys, $values)
	{
		$query = 'INTO ' . $table . ' (' . implode(', ', $keys). ') VALUES ';
        foreach($values as $v)
        {
            $query .= '('.implode(', ', $v).'), ';
        }
        return rtrim($query, ', ');
	}

	/**
	 * Update statement
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	$table      the table name
	 * @param	array	$values 	the update data
	 * @param	string	$where      the where conditions
	 * @param	array	$orderby	the orderby conditions
	 * @param	string	$limit      the limit
	 * @return	string
	 */
	public final function _update($table, $values, $where, $orderby = array(), $limit = FALSE)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}
		
		$limit = ( ! $limit) ? '' : ' LIMIT '.$limit;

		$orderby = (!empty($orderby))?' ORDER BY '.implode(', ', $orderby):'';

		$sql = 'UPDATE '.$table.' SET '.implode(', ', $valstr);

		$sql .= (!empty($where) ? ' WHERE '. $where : '');

		$sql .= $orderby.$limit;

		return $sql;
	}

	/**
	 * Delete statement
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param string $table table name
	 * @param string $where	where condition
	 * @return string
	 */
	public final function _delete($table, $where)
	{
		return 'DELETE FROM ' . $table . (!empty($where) ? ' WHERE '. $where : '');
	}
    
	/**
	 * Truncate statement
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * @param string $table table name
	 * @return string
	 */
	public final function _truncate($table)
	{
		return 'TRUNCATE TABLE ' . $table;
	}

	/**
	 * Execute the query
	 * @return resource
	 */
	public final function _execute()
	{
		return mysql_query( $this->_query_str, $this->_conn_id );
	}

	/**
	 * Escape String
	 *
	 * @param	mixed	$str	array given if we escape values to like operation
	 *							string in other cases
	 * @param	bool	$like	whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	public final function _escapeStr($str, $like = FALSE)
	{
		if (is_array($str))
		{
			foreach($str as $key => $val)
	   		{
				$str[$key] = '\''.$this->_escapeStr($val, $like).'\'';
	   		}
	   		return $str;
	   	}

		if (function_exists('mysql_real_escape_string') && is_resource($this->_conn_id))
		{
			$str = mysql_real_escape_string($str, $this->_conn_id);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			$str = mysql_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}
        
		return $like === true ? $this->escapeValToLike($str) : $str;
	}

	/**
	 * Escape the SQL Identifiers
	 * This function escapes column and table names
	 *
	 * @param	string $item string what need to escape
	 * @return	string
	 */
	public final function _escapeIdentifiers($item)
	{
		if ($this->_escape_char == '')
		{
			return $item;
		}

		foreach ($this->_reserved_identifiers as $id)
		{
			if (strpos($item, '.'.$id) !== FALSE)
			{
				$str = $this->_escape_char. str_replace('.', $this->_escape_char.'.', $item);

				// remove duplicates if the user already included the escape
				return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $str);
			}
		}

		if (strpos($item, '.') !== FALSE)
		{
			$str = $this->_escape_char.str_replace('.', $this->_escape_char.'.'.$this->_escape_char, $item).$this->_escape_char;
		}
		else
		{
			$str = $this->_escape_char.$item.$this->_escape_char;
		}

		// remove duplicates if the user already included the escape
		return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $str);
	}

	/**
	 * The error message string
	 * @return	string
	 */
	public final function _errorMessage()
	{
		return mysql_error($this->_conn_id);
	}

	/**
	 * The error message number
	 * @return	integer
	 */
	public final function _errorNumber()
	{
		return mysql_errno($this->_conn_id);
	}

	/**
	 * Affected Rows
	 * @return	integer
	 */
	public final function _affectedRows()
	{
		return mysql_affected_rows($this->_conn_id);
	}

	/**
	 * Insert ID
	 * @return	integer
	 */
	public final function _insertId()
	{
		return mysql_insert_id($this->_conn_id);
	}

	/****************************************
	 *			WORK WITH RESULT			*
	 ****************************************/

	/**
	 * Number of rows in the result set
	 * @return	integer
	 */
	public final function _numRows()
	{
		return mysql_num_rows($this->_result);
	}

	/**
	 * Number of fields in the result set
	 * @return	integer
	 */
	public final function _numFields()
	{
		return mysql_num_fields($this->_result);
	}

	/**
	 * Return total number of rows even with LIMIT query
	 * @return int
	 */
	public final function _foundRows()
	{
        return @mysql_result(mysql_query("SELECT FOUND_ROWS()", $this->_conn_id), 0);
	}

	/**
	 * Fetch Field Names
	 * Generates an array of column names
	 *
	 * @return	array
	 */
	public final function _listFields()
	{
		$field_names = array();
		while ($field = mysql_fetch_field($this->_result))
		{
			$field_names[] = $field->name;
		}

		return $field_names;
	}

	/**
	 * Generates an array of objects containing field meta-data
	 *
	 * @return	array
	 */
	public final function _fieldData()
	{
		$retval = array();
		while ($field = mysql_fetch_field($this->_result))
		{
			$F				= new stdClass();
			$F->name 		= $field->name;
			$F->type 		= $field->type;
			$F->default		= $field->def;
			$F->max_length	= $field->max_length;
			$F->primary_key = $field->primary_key;

			$retval[] = $F;
		}
		return $retval;
	}

	/**
	 * Free the result
	 */
	public final function _freeResult()
	{
		if (is_resource($this->_result))
		{
			mysql_free_result($this->_result);
			$this->_result = FALSE;
		}
	}

    /**
	 * Returns the result set as an array
	 *
	 * @return	array array of data
	 */
	public final function _fetch()
	{
        $rows = array();
        while($row = $this->_fetchAssoc())
            $rows[] = $row;
        return $rows;
	}
    /**
     * Return single result from 0 row and 0 field.
     * Other words return one cell
     * @return string
     */
    public final function _fetchOne()
    {
        return mysql_result($this->_result, 0);
    }
	/**
	 * Returns the result set as an array
	 *
	 * @return	array associative array
	 */
	public final function _fetchAssoc()
	{
		return mysql_fetch_assoc($this->_result);
	}

	/**
	 * Returns the result set as an object
	 *
	 * @return	object
	 */
	public final function _fetchObject()
	{
		return mysql_fetch_object($this->_result);
	}
    
    /****************************************
	 *			PROCEDURS AND ETC			*
	 ****************************************/
    
    /**
     * Return strings sql statements to call db procedure and get results
     * @param string $name procedure name to call
     * @param array $input [optional] input arguments. Notice: they come first in procedure declaration
     * @param array $output [optional] output arguments. Notice: they come withou '@' character.
     * Come last in procedure declaration
     * @return array in 0 key contains call procedure statement, in 1 key - select data statement
     */
    public final function _call($name, array $input=array(), array $output=array())
    {
        $return = array();
        
        $return[0] = 'CALL '.$name.'(';
        if(!empty($input))
        {
            $return[0] .= implode(',',(array)$input);
        }
        if(!empty($output))
        {
            if(!empty($input))
            {
                $return[0] .= ',';
            }
        
            foreach($output as &$o)
                $o = '@'.$o;
            $imp = implode(',',(array)$output);
            $return[0] .= $imp;
            $return[1] = 'SELECT '.$imp;
        }
        $return[0] .= ')';
        return $return;
    }
}

/* End of file mysql.php */
/* Location: ./class/Base/DB/drivers/mysql.php */
?>