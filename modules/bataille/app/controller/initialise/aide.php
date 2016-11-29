<?php
	require_once("test_connexion.php");
	$aide = new \modules\bataille\app\controller\Aide();

	$arr = \modules\bataille\app\controller\Bataille::getValues();