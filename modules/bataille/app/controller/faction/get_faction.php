<?php
	$faction = new \modules\bataille\app\controller\Faction();
	
	if (isset($_POST["id_faction"])) {
		$faction->getInfosFaction($_POST["id_faction"]);
	}
	else {
		$faction->getFactionPlayer();
		$faction->getPermissionsMembre($faction->getIdFaction());
	}
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();