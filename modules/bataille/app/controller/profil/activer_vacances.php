<?php
	
	if (\modules\bataille\app\controller\Profil::setActiverModeVacances() == true) {echo("ok");
		\core\auth\Connexion::setDeconnexion(WEBROOT."bataille/login");
	}
	else {
		header("location:".WEBROOT."bataille/gestion-profil");
	}
