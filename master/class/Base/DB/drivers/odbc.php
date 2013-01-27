<?php

abstract class odbcDbDriver extends DbDriver
{
	protected $_dbDriver = 'odbc';

	protected function _dbConnect()
	{
		echo '<pre>';
		var_dump('odbc connect');
		echo '</pre>';
	}
}

?>