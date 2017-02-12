<?php
	require_once("get_faction.php");
	
	$faction->getMembreFaction();
	$faction->getListPermissions();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();