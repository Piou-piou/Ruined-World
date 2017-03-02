<?php
	$message = new \modules\messagerie\app\controller\Messagerie();
	
	$message->setArchiverAllMessage();
	
	\core\HTML\flashmessage\FlashMessage::setFlash("Tous les messages ont été archivés", "success");
	
	header("location:".WEBROOT."messagerie");