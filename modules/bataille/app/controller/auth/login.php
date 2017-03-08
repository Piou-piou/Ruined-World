<?php
	$pseudo = $_POST['pseudo'];
	$mdp = $_POST['mdp'];
	$desactiver_vacances = $_POST["vacances_desactiver"];
	
	if ($desactiver_vacances == "desactiver") {
		$_SESSION['desactiver_vacances'] = 1;
	}
	
	\core\auth\Connexion::setLogin($pseudo, $mdp, WEBROOT."bataille/login", WEBROOT."bataille", 1);
