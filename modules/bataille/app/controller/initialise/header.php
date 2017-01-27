<?php
	require_once("test_connexion.php");
	use modules\bataille\app\controller\Bataille;
	
	Bataille::getNourriture();
	
	Bataille::getBase()->getMaBase();
	
	Bataille::getNation();
	
	$arr = Bataille::getValues();