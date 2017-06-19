<?php	namespace Helper;

	class Filter {
		
		/**
		 * Return data from global arrays (GET, POST, REQUEST, etc.)
		 * @param v string
		 * @param a string
		 * @param default mixed
		 */
		public static function get($v, $a = 'GET', $default = null){
			if (in_array($a, array('GET', 'POST')))
				$value = filter_input(constant('INPUT_' . strtoupper($a)), $v);
			else {
				$value = isset($a[$v]) ? (gettype($a[$v]) == 'array' ? $a[$v] : trim($a[$v])) : null;
			}
			return $value === null ? $default : $value;
		}
		
	}
	
?>