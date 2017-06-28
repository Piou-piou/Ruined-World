<?php
	$faction = new \modules\bataille\app\controller\Faction();
	
	if ($faction->setCreerFaction($_POST['nom_faction'], $_POST['description']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}