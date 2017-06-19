<?php	namespace Controller;

	class Index extends Base {
		
		public function index(){
			
			// show elements for parent=0 and only root level
			$tree = \Model\Comment::getTree(0, 1);
			
			// get user information for comments
			// do it separately to avoid join in \Model\Comment
			$user_ids = array_map(function($v){ return $v['user_id']; }, $tree);
			$user_ids = array_unique($user_ids);
			$users = \Model\User::getById($user_ids);
			
			// combine users and comments
			// prepare comments from tree for deletion (if user has cookie and can remove comment)
			$tree = array_map(function($v) use ($users){
				$v['user'] = $users[$v['user_id']];
				$v['editable'] = !empty($_COOKIE['user_comment']) && $_COOKIE['user_comment'] == $v['user']['secret_key'];
				return $v;
			}, $tree);
			
			$this->assign('tree_items', $tree);
			
		}
		
	}

?>