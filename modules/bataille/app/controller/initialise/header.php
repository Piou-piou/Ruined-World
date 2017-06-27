<?php
	require_once("test_connexion.php");
	use modules\bataille\app\controller\Bataille;
	$profil = new \modules\bataille\app\controller\Profil();
	
	if ($profil->getVacances() == false) {
		Bataille::getMissionsAleatoire()->setTerminerMissions();
		
		$nourriture = new \modules\bataille\app\controller\Nourriture();
		
		Bataille::getBase()->getMaBase();
		
		Bataille::getNation();
		
		$arr = Bataille::getValues();
		
		$messagerie = new \modules\messagerie\app\controller\Messagerie();
		$messagerie->getMessageNonLu();
		
		$arr = array_merge($arr, $messagerie->getValues());
	}
	else if (isset($_SESSION["desactiver_vacances"])) {
		if ($profil->getVacances() == ">48") {
			$profil->setDesactiverModeVacances();
			header("location:".WEBROOT."bataille");
		}
		else {
			\core\auth\Connexion::setDeconnexion(WEBROOT."bataille/login");
			die();
		}
	}
	else {
		\core\auth\Connexion::setDeconnexion(WEBROOT."bataille/login");
		die();
	}