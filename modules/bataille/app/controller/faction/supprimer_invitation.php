<?php
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	if ($faction->setSupprimerInvitation($_POST['id_identite']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}