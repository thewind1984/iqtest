<?php	namespace View;

	class Smarty extends Base implements IFace {
		
		var $obj;
		
		public function __construct(){
			require_once ROOT_DIR . '/libs/smarty/Smarty.class.php';
			$this->obj = new \Smarty();
			//$this->obj->compile_check = true;
			//$this->obj->debugging = false;
			//$this->obj->caching = false;
			//$this->obj->merge_compiled_includes = true;
			//$this->obj->cache_lifetime = 1;
		}
		
		public function get(){
			return $this->obj;
		}
		
		public function setTemplateDir($dir){
			$this->obj->setTemplateDir($dir);
		}
		
		public function setCacheDir($dir){
			$this->obj->setCacheDir($dir);
		}
		
	}
	
?>