<?php
	$query = \core\App::getDb()->select("ID_identite")->from("identite")->where("pseudo", "=", $_POST['pseudo'])->get();
	
	if (count($query) == 1) {
		foreach ($query as $obj) {
			$_SESSION['idlogin'.CLEF_SITE] = $obj->ID_identite;
		}
	}
	
	$profil = new \modules\bataille\app\controller\Profil();
	
	if ($profil->getVacances() == false) {
		echo("ok");
	}
	else if ($profil->getVacances() == "<48") {
		\core\HTML\flashmessage\FlashMessage::getFlash();
	}
	else {
		echo("popup");
	}
	
	$_SESSION['idlogin'.CLEF_SITE] = "";
	unset($_SESSION['idlogin'.CLEF_SITE]);