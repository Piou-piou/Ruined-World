<?php
	$missions = \modules\bataille\app\controller\Bataille::getMissionsAleatoire()->getMissions("nourriture");
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();