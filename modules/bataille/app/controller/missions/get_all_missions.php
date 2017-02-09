<?php
	$missions = \modules\bataille\app\controller\Bataille::getMissionsAleatoire()->getMissions();
	
	\modules\bataille\app\controller\Bataille::getUnite()->getAllUnites();
	
	\modules\bataille\app\controller\Bataille::getGoupeUnite()->getAllGroupeUnite();

	\modules\bataille\app\controller\Bataille::getBase()->getMaBase();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();