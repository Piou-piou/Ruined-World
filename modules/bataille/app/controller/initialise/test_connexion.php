<?php
	\core\auth\Connexion::setConnexion(WEBROOT."bataille/login");

	\core\auth\Connexion::setObgConnecte(WEBROOT."bataille/login");
	
	//test si on a une base dans l'url
	if (isset($_GET['base'])) {
		$_SESSION['id_base'] = $_GET['base'];
	}
	else if (isset($_POST['base'])) {
		$_SESSION['id_base'] = $_POST['base'];
	}
	else if (!isset($_SESSION['id_base'])) {
		$_SESSION['id_base'] = \modules\bataille\app\controller\Bataille::getFirstBase();
	}