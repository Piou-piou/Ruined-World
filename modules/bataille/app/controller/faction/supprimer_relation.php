<?php
	$faction = new \modules\bataille\app\controller\RelationFaction();
	$faction->getFactionPlayer();
	
	if ($faction->setSupprimerRelation($_POST['id_relation']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}