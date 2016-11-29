<?php
	$pseudo = $_POST['pseudo'];
	$mdp = $_POST['mdp'];

	\core\auth\Connexion::setLogin($pseudo, $mdp, WEBROOT."bataille/login", WEBROOT."bataille", 1);
