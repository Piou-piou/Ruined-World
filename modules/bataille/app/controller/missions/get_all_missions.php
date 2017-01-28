<?php
	$missions = \modules\bataille\app\controller\Bataille::getMissionsAleatoire()->getMissions();
	
	\modules\bataille\app\controller\Bataille::getUnite()->getAllUnites();
	
	/*$stockage = [
		"max_entrepot" => \modules\bataille\app\controller\Bataille::getBatiment()->getStockage(),
		"max_grenier" => \modules\bataille\app\controller\Bataille::getBatiment()->getStockage("grenier")
	];*/
	
	\modules\bataille\app\controller\Bataille::getBase()->getMaBase();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();
	
	/*$arr = array_merge($arr, $stockage);*/