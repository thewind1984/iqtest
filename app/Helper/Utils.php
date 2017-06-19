<?php	namespace Helper;

	class Utils {
		
		/**
		 * Generates new short code from a-zA-Z0-9
		 * @param length int		default length
		 */
		public static function shortCode($length = 20){
			$l = 'abcdefghijklmnopqrstuvwxyz';
			$l .= strtoupper($l) . '0123456789';
			$l = str_split($l);
			$code = '';
			for ($n=0; $n<$length; $n++)
				$code .= $l[array_rand($l)];
			return $code;
		}
		
	}
	
?>