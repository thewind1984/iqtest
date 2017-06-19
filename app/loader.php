<?php

	ini_set("display_errors", "off");
	error_reporting(E_ERROR);

	require_once APP_DIR . '/autoloader.php';
	
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$path = trim($path, '/');
	$path = $path ? explode('/', $path) : null;
	
	$controller = $path ? (sizeOf($path) > 1 ? array_slice($path, 0, -1) : $path) : array('index');
	$action = $path && sizeOf($path) > 1 ? end($path) : 'index';
	
	$controller = '\\Controller\\' . implode('\\', array_map(function($v){ return ucfirst(strtolower($v)); }, $controller));
	
	try {
		$class = new $controller($controller, $action);
	} catch (Exception $e) {
		die('<b>' . $e->getMessage() . '</b>');
	}
	
	$class->run();

?>