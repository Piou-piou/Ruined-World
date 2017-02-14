<?php
	$forum = new \modules\bataille\app\controller\ForumFaction();
	
	if ($forum->getFactionPlayer() == true) {
		$forum->getPermissionsMembre($forum->getIdFaction());
	}
	
	if ($forum->getForum($_POST['id_forum']) === true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();