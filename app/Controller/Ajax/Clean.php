<?php	namespace Controller\Ajax;

	class Clean extends \Controller\Ajax {
		
		public function index(){
			
			$conn = \Model\Db::getInstance();
			
			$st = $conn->prepare("TRUNCATE TABLE `comment`");
			$st->execute();
			
			$st = $conn->prepare("TRUNCATE TABLE `user`");
			$st->execute();
			
			return $this->render(1, 'ok');
			
		}
		
	}
	
?>