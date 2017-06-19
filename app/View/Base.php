<?php	namespace View;

	/**
	 * pattern: Singleton
	 */
	class Base {
		
		protected static $instance;
		public $tpl;
		
		private $header_file = 'Common/header.tpl';
		private $footer_file = 'Common/footer.tpl';
		
		/**
		 * Constructor
		 * Creates instance of template driver
		 */
		private function __construct(){
			$config = \Helper\Config::get('env');
			
			$class = '\\View\\' . $config['tpl_driver'];
			$this->tpl = new $class();
			$this->tpl = $this->tpl->get();		// final View component
			
			$this->tpl->setCacheDir(ROOT_DIR . '/' . $config['tpl_cache_dir']);
			$this->tpl->setCompileDir(ROOT_DIR . '/' . $config['tpl_cache_dir']);
			$this->tpl->setConfigDir(ROOT_DIR . '/' . $config['tpl_cache_dir']);
			$this->tpl->setTemplateDir($config['tpl_dir'] !== null ? ROOT_DIR . '/' . ltrim($config['tpl_dir'], '/') : APP_DIR . '/View/' . $config['tpl_driver']);
		}
		
		// hide because Singleton used
		private function __clone(){}
		
		/**
		 * Always return single instance of self
		 */
		public static function getInstance(){
			if (self::$instance == null)
				self::$instance = new self();
			
			return self::$instance;
		}
		
		/**
		 * Render template and output it
		 * @param vars array
		 * @param tpl string
		 * @param covers boolean
		 */
		public function render($vars, $tpl, $covers = true){
			foreach ((array)$vars as $k=>$v)
				$this->tpl->assign($k, $v);
			
			if (!$this->tpl->templateExists($tpl))
				throw new \Helper\Exception('Template not found (' . $tpl . ')');
			
			if ($covers && !empty($this->header_file))
				$this->tpl->display($this->header_file);
			
			if ($this->tpl->templateExists($tpl)) {
				$this->tpl->display($tpl);
			}
			
			if ($covers && !empty($this->footer_file))
				$this->tpl->display($this->footer_file);
		}
		
		/**
		 * Fetch template into variable without output
		 * @param vars array
		 * @param tpl string
		 */
		public function fetch($vars, $tpl){
			foreach ($vars as $k=>$v)
				$this->tpl->assign($k, $v);
			
			if (substr($tpl, 0, 7) != 'string:' && !$this->tpl->templateExists($tpl))
				throw new \Helper\Exception('Template not found (' . $tpl . ')');
			
			return $this->tpl->fetch($tpl);
		}
		
	}
	
?>