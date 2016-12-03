<?php
	\core\auth\Connexion::setConnexion(WEBROOT."bataille/login");

	\core\auth\Connexion::setObgConnecte(WEBROOT."bataille/login");

	//test si on a une base dans l'url
	if (isset($_POST['base'])) {
		$_SESSION['id_base'] = $_POST['base'];
	}
	else {
		$_SESSION['id_base'] = \modules\bataille\app\controller\Bataille::getFirstBase();
	}