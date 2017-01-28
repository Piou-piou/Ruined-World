<?php
	use \modules\bataille\app\controller\Bataille;
	
	Bataille::getBatiment()->getConstruction();
	
	Bataille::getBatiment()->getBatimentAConstruire();
	
	Bataille::getBase()->getBatimentsBase();
	
	Bataille::getUnite()->getAllUnites();
	
	Bataille::getUnite()->getRecrutement();
	
	Bataille::getCentreRecherche()->getRecherche();
	
	$marche = new \modules\bataille\app\controller\Marche();
	
	Bataille::getMissionsAleatoire();
	
	$arr = Bataille::getValues();