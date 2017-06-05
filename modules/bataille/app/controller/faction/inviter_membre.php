<?php
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	if ($faction->setInviterMembre($_POST['pseudo']) == true) {
	
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}