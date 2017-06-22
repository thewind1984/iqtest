<?php

	return [
		// output template section
		'tpl_cache_dir' => 'cache/tpl',		// must be writeable
		'tpl_driver' => 'Smarty',
		'tpl_dir' => null,		// default '\View\{tpl_driver}
		'tpl_use_cache' => true,
		
		// database section
		'db' => [
			'host' => 'localhost',
			'port' => 3306,
			'user' => 'iqcomments',
			'db' => 'iqcomments',
			'password' => 'iqcomments'
		],
		
		// extra environment data
		'debug' => false,
		'static_version' => '1.02',
	];

?>