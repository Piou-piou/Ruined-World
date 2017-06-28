<?php
	require_once("get_faction.php");
	
	$faction->getInvitationsEnvoyees();
	$faction->getListePermissions();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();