<?php	namespace View;

	interface IFace {
		
		public function __construct();
		public function get();
		public function setTemplateDir($dir);
		public function setCacheDir($dir);
		
	}