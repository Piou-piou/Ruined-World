<?php
	$forum = new \modules\bataille\app\controller\ForumFaction();
	$forum->getFactionPlayer();
	
	if ($forum->setCreerForum($_POST['titre'], $_POST['texte']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}