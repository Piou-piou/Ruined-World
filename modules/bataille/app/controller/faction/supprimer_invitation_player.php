<?php
	$faction = new \modules\bataille\app\controller\Faction();
	
	if ($faction->setSupprimerInvitationPlayer($_POST['id_faction']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}