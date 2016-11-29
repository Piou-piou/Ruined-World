<?php
	require_once(MODULEROOT."bataille/app/controller/initialise/test_connexion.php");

	$map = new \modules\bataille\app\controller\Map($_POST['id_base']);
	
	$arr = \modules\bataille\app\controller\Bataille::getValues();