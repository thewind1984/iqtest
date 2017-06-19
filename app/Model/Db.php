<?php	namespace Model;

	class Db extends Base {
		
		protected static $instance;
		private $connection;
		
		private function __construct(){
			$data = \Helper\Config::get('env', 'db');
			$this->connection = new \PDO('mysql:host=' . $data['host'] . ';port=' . $data['port'] . ';dbname=' . $data['db'] . ';charset=utf8', $data['user'], $data['password']);
		}
		
		private function __clone(){}
		
		public static function getInstance(){
			if (self::$instance == null) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		
		/**
		 * Redirect any methods to internal PDO interface
		 * @param method string
		 * @param args array
		 */
		public function __call($method, $args){
			return call_user_func_array([$this->connection, $method], $args);
		}
		
	}
	
?>