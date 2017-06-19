<?php	namespace Helper;

	class Exception {
		
		public function __construct($message){
			die("Error: {$message}");
		}
		
		private function trace(){
			$list = debug_backtrace();
			echo '<pre>' . $list . '</pre>';
		}
		
	}
	
?>