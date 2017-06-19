<?php	namespace Helper;

	class Config {
		
		private static $cached;
		
		/**
		 * Return any config data from 'config' file
		 * @param config string
		 * @param path string		delimiter = '/';
		 */
		public static function get($config, $path = null){
			$inner_path = md5($config . ($path ? $path : ''));
			if (isset(self::$cached[$inner_path]))
				return self::$cached[$inner_path];
			
			$file = ROOT_DIR . '/config/' . $config . '.php';
			
			if (!file_exists($file))
				return Array();
			
			if (!$path)
				return include($file);
			
			$path = $path ? "['" . (stripos($path, '/') !== false ? implode("']['", explode('/', $path)) : $path) . "']" : '';
			$data = include($file);
			
			eval('$data = isset($data' . $path . ') ? $data' . $path . ' : false;');
			
			self::$cached[$inner_path] = $data;
			return $data;
		}
		
	}
	
?>