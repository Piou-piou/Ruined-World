<?php
	
	\modules\bataille\app\controller\Bataille::getBase()->getBasesJoueur($_POST['id_identite']);
	
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer($_POST['id_identite']);
	
	$profil = new \modules\bataille\app\controller\Profil();
	
	\modules\bataille\app\controller\Bataille::setValues(["mode_vacances" => $profil->getTestModeVacances($_POST['id_identite'])]);
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();