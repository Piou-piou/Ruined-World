<?php
	require_once(MODULEROOT."bataille/app/controller/initialise/test_connexion.php");

	use \modules\bataille\app\controller\Bataille;

	Bataille::getIdBase();

	Bataille::getBatiment()->getUnBatiment($_POST['nom_batiment'], $_POST['emplacement']);

	$arr = \modules\bataille\app\controller\Bataille::getValues();