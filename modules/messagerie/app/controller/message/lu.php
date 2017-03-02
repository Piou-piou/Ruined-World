<?php
	$message = new \modules\messagerie\app\controller\Messagerie();
	
	$message->setLireAllMessage();
	
	\core\HTML\flashmessage\FlashMessage::setFlash("Tous les messages ont été lu", "success");
	
	header("location:".WEBROOT."messagerie");