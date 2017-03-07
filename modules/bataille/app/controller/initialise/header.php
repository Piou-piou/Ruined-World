<?php
	use modules\bataille\app\controller\Bataille;
	$profil = new \modules\bataille\app\controller\Profil();
	
	if ($profil->getVacances() == false) {
		require_once("test_connexion.php");
		
		Bataille::getMissionsAleatoire()->setTerminerMissions();
		
		$nourriture = new \modules\bataille\app\controller\Nourriture();
		
		Bataille::getBase()->getMaBase();
		
		Bataille::getNation();
		
		$arr = Bataille::getValues();
		
		$messagerie = new \modules\messagerie\app\controller\Messagerie();
		$messagerie->getMessageNonLu();
		
		$arr = array_merge($arr, $messagerie->getValues());
	}
	else {
		\core\auth\Connexion::setDeconnexion(WEBROOT."bataille/login");
		die();
	}