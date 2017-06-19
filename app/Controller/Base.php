<?php	namespace Controller;

	class Base {
		
		protected $controller;
		protected $action;
		protected $template;
		protected $routed = false;
		protected $rendering = true;
		protected $tpl_vars_scope = [];
		protected $view;
		
		/**
		 * Constructor
		 * @param controller string
		 * @param action string
		 */
		public function __construct($controller, $action){
			$this->assign('static_version', \Helper\Config::get('env', 'static_version'));
			
			$this->controller = $controller;
			$this->action = $action;
			
			$this->view = \View\Base::getInstance();
		}
		
		/**
		 * Main method after init
		 */
		public function run(){
			call_user_func_array([$this, $this->action], []);
			
			if (!$this->routed && $this->rendering)
				$this->render();
		}
		
		/**
		 * Routing to another router's path
		 * @param class string		additional class
		 * @param action string		action to execute
		 */
		protected function route($class, $action){
			$controller = $this->controller . '\\' . implode('\\', array_map(function($v){ return ucfirst(strtolower($v)); }, explode('/', $class)));
			
			$this->routed = true;
			
			$class = new $controller($controller, $action);
			$class->run();
		}
		
		public function __call($method, $args){
			if (method_exists($this, 'index'))
				call_user_func_array([$this, 'index'], []);
		}
		
		/**
		 * Put some variable into inner scope for output
		 * @param k string
		 * @param v mixed
		 */
		protected function assign($k, $v){
			$this->tpl_vars_scope[$k] = $v;
		}
		
		/**
		 * Render output template with inner scope of data
		 */
		protected function render(){
			$controller_name = explode('\\', trim($this->controller, '\\'));
			$this->view->render(
				$this->tpl_vars_scope,
				end($controller_name) . '/' . ($this->template ? $this->template : $this->action) . '.tpl'
			);
		}
		
	}
	
?>