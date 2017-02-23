<?php
	require_once("test_connexion.php");
	use modules\bataille\app\controller\Bataille;
	
	Bataille::getMissionsAleatoire()->setTerminerMissions();
	
	$nourriture = new \modules\bataille\app\controller\Nourriture();
	
	Bataille::getBase()->getMaBase();
	
	Bataille::getNation();
	
	$arr = Bataille::getValues();
	
	$messagerie = new \modules\messagerie\app\controller\Messagerie();
	$messagerie->getMessageNonLu();
	
	$arr = array_merge($arr, $messagerie->getValues());