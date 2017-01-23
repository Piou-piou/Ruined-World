<?php
	use \modules\bataille\app\controller\Bataille;
	
	Bataille::getBatiment()->getConstruction();
	
	Bataille::getBatiment()->getBatimentAConstruire();
	
	Bataille::getBase()->getBatimentsBase();
	
	Bataille::getUnite()->getAllUnites();
	
	Bataille::getCentreRecherche()->getRecherche();
	
	$arr = Bataille::getValues();