<?php
	if (\modules\bataille\app\controller\Bataille::getBatiment()->getEmplacementConstructionLibre($_POST['pos_depart'], $_POST['nom_batiment'], $_POST['nom_batiment_sql']) == false) {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}