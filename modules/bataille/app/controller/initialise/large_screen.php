<?php
	use \modules\bataille\app\controller\Bataille;
	
	Bataille::getBatiment()->getConstruction();
	
	Bataille::getBatiment()->getBatimentAConstruire();
	
	Bataille::getBase()->getBatimentsBase();
	
	Bataille::getUnite()->getAllUnites();
	
	$arr = Bataille::getValues();