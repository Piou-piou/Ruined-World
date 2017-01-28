<?php
	require_once("test_connexion.php");
	use modules\bataille\app\controller\Bataille;
	
	Bataille::getNourriture();
	
	Bataille::getBase()->getMaBase();
	
	Bataille::getNation();
	
	//\modules\bataille\app\controller\GenerationRapport::setGenererRapport("rapport de mission", "toto", "mission");
	
	$arr = Bataille::getValues();