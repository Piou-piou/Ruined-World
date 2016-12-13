<?php
	if (\modules\bataille\app\controller\Bataille::getUnite()->setCommencerRecruter($_POST['nom'], $_POST['type'], $_POST['nombre_recruter']) == false) {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}
	else {
		echo("ok");
	}