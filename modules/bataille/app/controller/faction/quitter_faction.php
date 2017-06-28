<?php
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	if ($faction->setQuitterFaction() == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}