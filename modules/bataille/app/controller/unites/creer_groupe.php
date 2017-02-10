<?php
	use \modules\bataille\app\controller\Bataille;
	
	if (Bataille::getGoupeUnite()->setCreerGroupe($_POST['nombre_unite'], $_POST['nom_unite'], $_POST['type_unite'], $_POST['nom_groupe']) == true) {
		echo("ok");
	}
	else {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}