<?php
	$forum = new \modules\bataille\app\controller\ForumFaction();
	$forum->getFactionPlayer();
	
	if ($forum->setSupprimerCommentaire($_POST['id_commentaire']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}