<?php
try {
	/**
	 * @global APP global constant show that we came into application with right way
	 */
	define('APP', true);
	require_once 'class/Base/Loader.php';

	Loader::loadClass('Base_Manager');

	$manager = App::manager();
	$manager->run();
} catch (Exception $exc) {
	try {
		if (isset($manager))
			$manager->error($exc);
		else
		{
			echo 'Unknown Exception <pre>' .$exc . '</pre>';
		}
	} catch (Exception $exc) {
		echo 'Error<br/><pre>' . $exc . '</pre>';
	}
}

?>
