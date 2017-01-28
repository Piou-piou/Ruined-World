<?php
	$missions = \modules\bataille\app\controller\Bataille::getMissionsAleatoire()->getMissions();
	
	\modules\bataille\app\controller\Bataille::getUnite()->getAllUnites();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();