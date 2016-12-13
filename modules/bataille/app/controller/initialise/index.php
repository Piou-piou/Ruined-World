<?php
	require_once("test_connexion.php");
	use modules\bataille\app\controller\Bataille;

	Bataille::getBatiment()->getConstruction();

	Bataille::getBase()->getMaBase();

	Bataille::getNation();

	$marche = new \modules\bataille\app\controller\Marche();

	Bataille::getUnite()->getRecrutement();

	$arr = Bataille::getValues();