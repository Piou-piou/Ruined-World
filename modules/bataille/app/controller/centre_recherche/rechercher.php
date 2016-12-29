<?php
	if (\modules\bataille\app\controller\Bataille::getCentreRecherche()->setCommencerRecherche($_POST['recherche'], $_POST['type']) == false) {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}
	else {
		echo("ok");
	}