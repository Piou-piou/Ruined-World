<?php
	
	\modules\bataille\app\controller\Bataille::getBase()->getBasesJoueur($_POST['id_identite']);
	
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();