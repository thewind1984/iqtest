<?php

	set_include_path(get_include_path() . PATH_SEPARATOR . APP_DIR);
	set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_DIR . '/libs/smarty/sysplugins');

	spl_autoload_register(function($class){
		if (strtolower(substr($class, 0, 6)) == 'smarty')
			$class_file = strtolower($class) . '.php';
		else {
			$class_file = APP_DIR . '/' . str_replace('\\', '/', $class) . '.php';
			//echo $class_file . '<br>';
			
			if (!file_exists($class_file)) {
				throw new Exception('Class not found (' . $class . ')');
			}
		}
		
		include_once $class_file;
		
		return true;
	});
	
?>