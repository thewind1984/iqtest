<?php	namespace Controller\Ajax;

	use \Helper\Filter;
	use \Helper\Tree;
	use \Model\Comment as MC;
	use \Model\User;

	class Comment extends \Controller\Ajax {
		
		private $comment;
		private $user;
		
		/**
		 * Sub-tree
		 */
		public function index(){
			$parent_id = intval(Filter::get('parent_id', 'GET', 0));
			
			$tree = Tree::build($parent_id);
			
			return $this->render(static::RESPONSE_STATUS_SUCCESS, 'ok', [
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
			
			$user_name = Filter::get('user_name', 'POST', null);
			$user_email = Filter::get('user_email', 'POST', null);
			$text = Filter::get('comment', 'POST', null);
			$parent_id = intval(Filter::get('parent_id', 'POST', 0));
			
			// create user (if exist => return existing id)
			// using static method
			$user_id = User::add($user_name, $user_email);
			if ($user_id === false)
				return $this->render(static::RESPONSE_STATUS_FAIL, User::getError());
			
			// adding new comment
			// using dynamic methods
			$comment = new MC();
			$comment->user_id = $user_id;
			$comment->text = $text;
			$comment->parent_id = $parent_id;
			$comment_id = $comment->save();
			
			if ($comment_id === false)
				return $this->render(static::RESPONSE_STATUS_FAIL, MC::getError());
			
			$comment_data = MC::getById($comment_id);
			$comment_data['user'] = User::getById($comment_data['user_id'], true);
			$comment_data['editable'] = true;
			
			return $this->render(static::RESPONSE_STATUS_SUCCESS, 'ok', [
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
			$comment_id = intval(Filter::get('id', 'POST', 0));
			
			$access = $this->checkCommentAccess($comment_id);
			if ($access !== true)
				return $access;
			
			MC::delete($comment_id);
			
			$this->comment['user'] = $this->user;
			$this->comment['status'] = 0;
			
			return $this->render(static::RESPONSE_STATUS_SUCCESS, 'ok', [
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
			
			$comment_id = intval(Filter::get('id', $method, 0));
			
			$access = $this->checkCommentAccess($comment_id);
			if ($access !== true)
				return $access;
			
			if ($method == 'GET') {
				
				return $this->render(static::RESPONSE_STATUS_SUCCESS, 'ok', [
					'id' => $this->comment['id'],
					'content' => $this->view->fetch([
						'comment' => $this->comment,
					],
					'Comment/edit.tpl')
				]);
				
			} else {
				
				$text = Filter::get('comment', 'POST', null);
				
				$result = MC::updateText($comment_id, $text);
				if ($result !== true)
					return $this->render(static::RESPONSE_STATUS_FAIL, MC::getError());
				
				$comment = MC::getById($comment_id);
				
				return $this->render(static::RESPONSE_STATUS_SUCCESS, 'ok', [
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
			$this->comment = MC::getById($comment_id);
			if (empty($this->comment['id']))
				return $this->render(static::RESPONSE_STATUS_FAIL, 'Неверные данные');
			
			$this->user = User::getById($this->comment['user_id'], true);
			if (empty($_COOKIE['user_comment']) || $_COOKIE['user_comment'] != $this->user['secret_key'])
				return $this->render(static::RESPONSE_STATUS_FAIL, 'Чужой комментарий');
			
			return true;
		}
		
	}
	
?>