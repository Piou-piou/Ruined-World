<?php
	use \modules\bataille\app\controller\Bataille;
	
	Bataille::getBatiment()->getConstruction();
	
	Bataille::getBase()->getBatimentsBase();
	
	Bataille::getUnite()->getAllUnites();
	
	$arr = Bataille::getValues();