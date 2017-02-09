<?php
	
	$groupe = new \modules\bataille\app\controller\GroupeUnite();
	$groupe->getAllGroupeUnite();
	
	\modules\bataille\app\controller\Bataille::getUnite()->getAllUnites();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();