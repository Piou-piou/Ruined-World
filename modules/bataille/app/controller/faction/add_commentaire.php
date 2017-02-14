<?php
	$forum = new \modules\bataille\app\controller\ForumFaction();
	$forum->getFactionPlayer();
	
	if ($forum->setAjouterCommentaire($_POST['commentaire'], $_POST['id_forum']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}