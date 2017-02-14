<?php
	$permission = new \modules\bataille\app\controller\Faction();
	$permission->getFactionPlayer();
	
	$permission->setGererPermission($_POST['id_permission'], $_POST['id_identite'], $permission->getIdFaction(), $_POST['type']);
	
	\core\HTML\flashmessage\FlashMessage::getFlash();