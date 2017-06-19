<?php	namespace Model;

	class Comment extends Base {
		
		private $user_id;
		private $text;
		private $parent_id;
		const db_table = 'comment';
		
		/**
		 * Magic set inner attribute
		 * @param k string
		 * @param v mixed
		 */
		public function __set($k, $v){
			$this->$k = $v;
		}
		
		/**
		 * Prepare types and values of inner attributes
		 */
		private function prepareData(){
			$this->text = trim($this->text);
			$this->user_id = intval($this->user_id);
			$this->parent_id = intval($this->parent_id);
		}
		
		/**
		 * Add new comment
		 * @param user_name string
		 * @param user_email string
		 */
		public function save(){
			$this->prepareData();
			
			if (!$this->text) {
				return static::setError('пустой текст');
			}
			
			if (!$this->user_id) {
				return static::setError('неверный пользователь');
			}
			
			list ($level, $right_key) = $this->parent_id
				? $this->getDataById($this->parent_id)
				: [0, $this->getMaxRightKey()];
			
			$conn = Db::getInstance();
			
			if ($this->parent_id) {
				$st = $conn->prepare("UPDATE `" . static::db_table . "` SET `left_key` = `left_key`+2 WHERE `right_key` > ? AND `left_key` > ?");
				$st->execute([$right_key, $right_key]);
				
				$st = $conn->prepare("UPDATE `" . static::db_table . "` SET `right_key` = `right_key`+2 WHERE `right_key` >= ?");
				$st->execute([$right_key]);
			} else {
				$right_key++;
			}
			
			// `date` and `status` will be automatically filled in
			$st = $conn->prepare("INSERT INTO `" . static::db_table . "` SET `left_key` = :right_key, `right_key` = :right_key+1, `level` = :level+1, `parent_id` = :parent_id, `user_id` = :user_id, `text` = :text");
			$st->bindParam(':right_key', $right_key, \PDO::PARAM_INT);
			$st->bindParam(':level', $level, \PDO::PARAM_INT);
			$st->bindParam(':parent_id', $this->parent_id, \PDO::PARAM_INT);
			$st->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
			$st->bindParam(':text', $this->text, \PDO::PARAM_STR);
			$st->execute();
			
			$comment_id = $conn->lastInsertId();
			
			return $comment_id;
		}
		
		/**
		 * Return single row data by its id
		 * @param id int
		 * @param assoc boolean
		 */
		private static function getDataById($id, $assoc = false){
			$conn = Db::getInstance();
			$st = $conn->prepare("SELECT `level`, `right_key`, `left_key` FROM `" . static::db_table . "` WHERE `id`=:id");
			$st->bindParam(':id', $id, \PDO::PARAM_INT);
			$st->execute();
			return $st->fetch($assoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM);
		}
		
		/**
		 * Return full single row data by its id
		 * @param id int
		 */
		public static function getById($id){
			$conn = Db::getInstance();
			$st = $conn->prepare("SELECT p_table.*, (SELECT COUNT(*) FROM `" . static::db_table . "` WHERE `parent_id` = p_table.`id`) as `children` FROM `" . static::db_table . "` p_table WHERE p_table.`id`=:id");
			$st->bindParam(':id', $id, \PDO::PARAM_INT);
			$st->execute();
			return $st->fetch(\PDO::FETCH_ASSOC);
		}
		
		/**
		 * Return maximum right key from whole table
		 */
		private function getMaxRightKey(){
			$conn = Db::getInstance();
			$st = $conn->prepare("SELECT MAX(`right_key`) FROM `" . static::db_table . "`");
			$st->execute();
			return intval($st->fetchColumn());
		}
		
		/**
		 * Select full tree of comments
		 */
		public static function getTree($parent_id = 0, $max_level = null){
			$conn = Db::getInstance();
			
			$inputarr = [];
			
			$max_level = intval($max_level);
			$parent_id = intval($parent_id);
			
			if ($parent_id > 0) {
				$parent_data = self::getDataById($parent_id, true);
				$inputarr = array_merge($inputarr, [$parent_data['left_key'], $parent_data['right_key']]);
			}
			if ($max_level > 0) {
				$inputarr[] = $max_level;
			}
			
			$st = $conn->prepare("
				SELECT p_table.*, (SELECT COUNT(*) FROM `" . static::db_table . "` WHERE `parent_id` = p_table.`id`) as `children`
				FROM `" . static::db_table . "` p_table
				WHERE 1"
				. ($parent_id > 0 ? " AND p_table.`left_key` > ? AND p_table.`right_key` < ?" : "")
				. ($max_level > 0 ? " AND p_table.`level` <= ?" : "")
				. " ORDER BY p_table.`left_key`"
			);
			$st->execute($inputarr);
			
			$data = $st->fetchAll(\PDO::FETCH_ASSOC);
			return parent::makeAssocArray($data, 'id');
		}
		
		/**
		 * Delete existing comment by id
		 * @param id integer
		 */
		public static function delete($id){
			$conn = Db::getInstance();
			
			$st = $conn->prepare("UPDATE `" . static::db_table . "` SET `status` = ? WHERE `id` = ?");
			$st->execute([0, $id]);
			
			return true;
		}
		
		/**
		 * Update comment's text by its id
		 * @param id int
		 * @param text string
		 */
		public static function updateText($id, $text){
			$conn = Db::getInstance();
			
			$text = trim($text);
			
			if (!$text) {
				return static::setError('пустой текст');
			}
			
			$st = $conn->prepare("UPDATE `" . static::db_table . "` SET `text` = ? WHERE `id` = ?");
			$st->execute([$text, $id]);
			
			return true;
		}
		
	}