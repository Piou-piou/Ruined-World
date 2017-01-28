<?php
	require_once(MODULEROOT."bataille/app/controller/initialise/header.php");
	
	$messagerie = new \modules\messagerie\app\controller\Messagerie();

	if ($messagerie->getUnMessage(\modules\messagerie\app\controller\Messagerie::$url_message) === false) {
		\core\HTML\flashmessage\FlashMessage::setFlash("Message inexistant");
		header("location:".WEBROOT."messagerie");
	}
	
	$arr = array_merge($arr, $messagerie->getValues());