<?php	namespace Controller\Ajax;

	use \Helper\Filter;
	use \Helper\Tree;

	class Comment extends \Controller\Ajax {
		
		private $comment;
		private $user;
		
		/**
		 * Sub-tree
		 */
		public function index(){
			$parent_id = intval(Filter::get('parent_id', 'GET', 0));
			
			$tree = Tree::build($parent_id);
			
			return $this->render(1, 'ok', [
				'parent_id' => $parent_id,
				'content' => $this->view->fetch([
					'parent_id' => $parent_id,
					'tree_items' => $tree
				],
				'Comment/tree.tpl')
			]);
		}
		
		/**
		 * Add new comment
		 */
		public function add(){
			
			$user_name = \Helper\Filter::get('user_name', 'POST', null);
			$user_email = \Helper\Filter::get('user_email', 'POST', null);
			$text = \Helper\Filter::get('comment', 'POST', null);
			$parent_id = intval(\Helper\Filter::get('parent_id', 'POST', 0));
			
			// create user (if exist => return existing id)
			// using static method
			$user_id = \Model\User::add($user_name, $user_email);
			if ($user_id === false)
				return $this->render(0, \Model\User::getError());
			
			// adding new comment
			// using dynamic methods
			$comment = new \Model\Comment();
			$comment->user_id = $user_id;
			$comment->text = $text;
			$comment->parent_id = $parent_id;
			$comment_id = $comment->save();
			
			if ($comment_id === false)
				return $this->render(0, \Model\Comment::getError());
			
			$comment_data = \Model\Comment::getById($comment_id);
			$comment_data['user'] = \Model\User::getById($comment_data['user_id'], true);
			$comment_data['editable'] = true;
			
			return $this->render(1, 'ok', [
				'parent_id' => $parent_id,
				'comment_id' => $comment_id,
				'comment_item' => $this->view->fetch(
					[
						'comment' => $comment_data,
						'is_new' => true
					],
					'Comment/item.tpl'
				)
			]);
		}
		
		/**
		 * Delete existing comment
		 */
		public function delete(){
			$comment_id = intval(\Helper\Filter::get('id', 'POST', 0));
			
			$access = $this->checkCommentAccess($comment_id);
			if ($access !== true)
				return $access;
			
			\Model\Comment::delete($comment_id);
			
			$this->comment['user'] = $this->user;
			$this->comment['status'] = 0;
			
			return $this->render(1, 'ok', [
				'id' => $this->comment['id'],
				'content' => $this->view->fetch([
					'comment' => $this->comment,
				],
				'Comment/item.tpl')
			]);
		}
		
		/**
		 * Edit existing comment
		 * GET request => edit form
		 * POST request => saving
		 */
		public function edit(){
			$method = $_SERVER['REQUEST_METHOD'];
			$method = in_array($method, ['GET', 'POST']) ? $method : 'GET';
			
			$comment_id = intval(\Helper\Filter::get('id', $method, 0));
			
			$access = $this->checkCommentAccess($comment_id);
			if ($access !== true)
				return $access;
			
			if ($method == 'GET') {
				
				return $this->render(1, 'ok', [
					'id' => $this->comment['id'],
					'content' => $this->view->fetch([
						'comment' => $this->comment,
					],
					'Comment/edit.tpl')
				]);
				
			} else {
				
				$text = \Helper\Filter::get('comment', 'POST', null);
				
				$result = \Model\Comment::updateText($comment_id, $text);
				if ($result !== true)
					return $this->render(0, \Model\Comment::getError());
				
				$comment = \Model\Comment::getById($comment_id);
				
				return $this->render(1, 'ok', [
					'id' => $comment['id'],
					'content' => $this->view->fetch([
						'comment' => $comment,
					],
					'Comment/text.tpl')
				]);
			}
		}
		
		/**
		 * Check if comment is accessible by current user
		 * @param comment_id int
		 */
		private function checkCommentAccess($comment_id) {
			$this->comment = \Model\Comment::getById($comment_id);
			if (empty($this->comment['id']))
				return $this->render(0, 'Неверные данные');
			
			$this->user = \Model\User::getById($this->comment['user_id'], true);
			if (empty($_COOKIE['user_comment']) || $_COOKIE['user_comment'] != $this->user['secret_key'])
				return $this->render(0, 'Чужой комментарий');
			
			return true;
		}
		
	}
	
?>