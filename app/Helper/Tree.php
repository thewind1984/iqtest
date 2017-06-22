<?php	namespace Helper;

	use \Model\Comment;
	use \Model\User;

	class Tree {
		
		public static function build($parent_id = 0, $max_level = null){
			
			// show elements for parent=0 and only root level
			$tree = Comment::getTree($parent_id, $max_level);
			
			// get user information for comments
			// do it separately to avoid join in \Model\Comment
			$user_ids = array_map(function($v){ return $v['user_id']; }, $tree);
			$user_ids = array_unique($user_ids);
			$users = User::getById($user_ids);
			
			// combine users and comments
			// prepare comments from tree for deletion (if user has cookie and can remove comment)
			$tree = array_map(function($v) use ($users){
				$v['user'] = $users[$v['user_id']];
				$v['editable'] = !empty($_COOKIE['user_comment']) && $_COOKIE['user_comment'] == $v['user']['secret_key'];
				return $v;
			}, $tree);
			
			return $tree;
			
		}
		
	}