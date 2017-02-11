<?php
	require_once("get_faction.php");
	
	$faction->getMembreFaction();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();