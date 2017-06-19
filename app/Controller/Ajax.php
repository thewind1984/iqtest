<?php	namespace Controller;

	class Ajax extends Base {
		
		public function __construct($controller, $action){
			parent::__construct($controller, $action);
			$this->rendering = false;
		}
		
		public function __call($method, $args){
			try {
				$this->route($method, 'index', $args);
			} catch (Exception $e) {
				echo $e->getMessage();
				die();
			}
		}
		
		public function index(){}
		
		/**
		 * Own 'render' method for /ajax/... URLs
		 * Outputs JSON encoded data
		 */
		protected function render($status = 0, $message = '', $data = array()){
			echo json_encode(array_merge(array(
				'status' => $status,
				'message' => $message,
			), $data));
		}
		
	}
	
?>