<?php
	require_once("get_faction.php");
	
	$faction->getInvitationsMembre();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();