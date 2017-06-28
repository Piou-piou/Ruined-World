<?php
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	if ($faction->setChangerChef($_POST['id_identite']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}