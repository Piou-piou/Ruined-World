<?php
	use \modules\bataille\app\controller\Bataille;
	
	Bataille::getBatiment()->getConstruction();
	
	Bataille::getBase()->getBatimentsBase();
	
	Bataille::getUnite()->getAllUnites();
	
	Bataille::getUnite()->getRecrutement();
	
	Bataille::getCentreRecherche()->getRecherche();
	
	$marche = new \modules\bataille\app\controller\Marche();
	
	Bataille::getMissionsAleatoire();
	
	Bataille::getMissionsAleatoire()->setTerminerMissions();
	
	$arr = Bataille::getValues();