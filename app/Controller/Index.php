<?php	namespace Controller;

	use \Helper\Tree;

	class Index extends Base {
		
		const ROOT_PARENT_ID = 0;
		const MAX_SINGLE_LEVEL = 1;
		
		public function index(){
			
			$tree = Tree::build(static::ROOT_PARENT_ID, static::MAX_SINGLE_LEVEL);
			
			$this->assign('tree_items', $tree);
			
		}
		
	}

?>