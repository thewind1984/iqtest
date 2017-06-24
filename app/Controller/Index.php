<?php	namespace Controller;

	use \Helper\Tree;
	use \Model\Comment;

	class Index extends Base {
		
		public function index(){
			
			$tree = Tree::build(Comment::ROOT_PARENT_ID, Comment::MAX_SINGLE_LEVEL);
			
			$this->assign('tree_items', $tree);
			
		}
		
	}

?>