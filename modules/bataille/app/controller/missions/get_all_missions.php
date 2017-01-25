<?php
	$missions = \modules\bataille\app\controller\Bataille::getMissionsAleatoire()->getMissions("nourriture");
	
	\modules\bataille\app\controller\Bataille::getUnite()->getAllUnites();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();
	
	header("location:".WEBROOT."bataille");