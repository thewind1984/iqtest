<?php	namespace Model;

	class User extends Base {
		
		const db_table = 'user';
		
		/**
		 * Add new user
		 * @param user_name string
		 * @param user_email string
		 */
		public static function add($user_name, $user_email){
			if (!trim($user_name)) {
				return static::setError('Неверное имя');
			}
			
			if (!trim($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
				return static::setError('Неверный email');
			}
			
			$conn = \Model\Db::getInstance();
			
			$user_id = null;
			$user_name = trim($user_name);
			$user_email = trim($user_email);
			
			$user_id = self::findByEmail($user_email);
			
			if ($user_id === false) {
			
				$secret_key = \Helper\Utils::shortCode();
				
				$st = $conn->prepare("INSERT IGNORE INTO `" . static::db_table . "` (`id`,`name`,`email`,`secret_key`) VALUES (:id, :name, :email, :secret)");
				$st->bindParam(':id', $user_id);
				$st->bindParam(':name', $user_name, \PDO::PARAM_STR);
				$st->bindParam(':email', $user_email, \PDO::PARAM_STR);
				$st->bindParam(':secret', $secret_key, \PDO::PARAM_STR);
				$st->execute();
				
				if (($error = $st->errorCode()) != '00000') {
					return static::setError($st->errorInfo());
				}
				
				$user_id = $conn->lastInsertId();
				
			}
			
			$st = $conn->prepare("SELECT `secret_key` FROM `" . static::db_table . "` WHERE `id` = ?");
			$st->execute([$user_id]);
			$secret_key = $st->fetchColumn();
			
			// set 'secret' cookie for 1 month for delete / edit comment
			setcookie('user_comment', $secret_key, time() + 30*24*60*60, '/', '.' . $_SERVER['SERVER_NAME']);
			
			return $user_id;
		}
		
		/**
		 * Find user by its email
		 * @param email string
		 */
		public static function findByEmail($email){
			$conn = Db::getInstance();
			
			$st = $conn->prepare("SELECT `id` FROM `" . static::db_table . "` WHERE `email` = :email");
			$st->bindParam(':email', $email, \PDO::PARAM_STR);
			$st->execute();
			return $st->fetchColumn();
		}
		
		/**
		 * Return information about users by its id
		 * @param id int | array
		 * @param single boolean
		 */
		public static function getById($id, $single = false){
			$id_set = gettype($id) == 'array' ? array_values($id) : [$id];
			
			$conn = Db::getInstance();
			$st = $conn->prepare("SELECT * FROM `" . static::db_table . "` WHERE `id` IN (" . implode(',', array_fill(0, sizeOf($id_set), '?')) . ")");
			$st->execute($id_set);
			
			$data = $st->fetchAll(\PDO::FETCH_ASSOC);
			$data = parent::makeAssocArray($data, 'id');
			return $single && sizeOf($id_set) == 1 ? (isset($data[$id]) ? $data[$id] : false) : $data;
		}
		
	}