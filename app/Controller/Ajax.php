<?php	namespace Controller;

	class Ajax extends Base {
		
		const RESPONSE_STATUS_FAIL = 0;
		const RESPONSE_STATUS_SUCCESS = 1;
		
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
		protected function render($status = static::RESPONSE_STATUS_FAIL, $message = '', $data = []){
			echo json_encode(array_merge([
				'status' => $status,
				'message' => $message,
			]), $data));
		}
		
	}
	
?>