<?php
	$faction = new \modules\bataille\app\controller\RelationFaction();
	$faction->getFactionPlayer();
	
	if ($faction->setAjouterRelation($_POST['nom_faction'], $_POST['relation']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}