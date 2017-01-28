<?php
	require_once("test_connexion.php");
	use modules\bataille\app\controller\Bataille;
	
	Bataille::getNourriture();
	
	Bataille::getBase()->getMaBase();
	
	Bataille::getNation();
	
	Bataille::getMissionsAleatoire()->setTerminerMissions();
	
	$arr = Bataille::getValues();
	
	$messagerie = new \modules\messagerie\app\controller\Messagerie();
	$messagerie->getMessageNonLu();
	
	$arr = array_merge($arr, $messagerie->getValues());