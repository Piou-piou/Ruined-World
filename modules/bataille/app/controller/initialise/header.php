<?php
	require_once("test_connexion.php");
	use modules\bataille\app\controller\Bataille;
	
	Bataille::getBase()->getMaBase();
	
	Bataille::getNation();
	
	Bataille::getNourriture();
	
	$arr = Bataille::getValues();