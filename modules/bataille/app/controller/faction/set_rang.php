<?php
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	if ($faction->setRang($_POST['id_identite'], $_POST['rang']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}