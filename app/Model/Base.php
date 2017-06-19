<?php	namespace Model;

	class Base {
		
		protected static $error;
		
		public static function getError(){
			$error = static::$error;
			static::$error = null;
			return $error;
		}
		
		protected static function setError($error){
			static::$error = $error;
			return false;
		}
		
		/**
		 * Converts numeric array to assoc array with 'field' as key
		 * @param data array
		 * @param field string
		 */
		protected static function makeAssocArray($data, $field){
			$list = [];
			foreach ($data as $v)
				$list[$v[$field]] = $v;
			
			return $list;
		}
		
	}
	
?>