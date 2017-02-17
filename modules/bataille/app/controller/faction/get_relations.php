<?php
	$relation = new \modules\bataille\app\controller\RelationFaction();
	
	if ($relation->getFactionPlayer() == true) {
		$relation->getPermissionsMembre($relation->getIdFaction());
	}
	
	$relation->getListeRelation();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();