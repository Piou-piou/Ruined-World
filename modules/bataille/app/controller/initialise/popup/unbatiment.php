<?php
	require_once(MODULEROOT."bataille/app/controller/initialise/test_connexion.php");

	use \modules\bataille\app\controller\Bataille;

	Bataille::getIdBase();

	if (Bataille::getBatiment()->getUnBatiment($_POST['nom_batiment'], $_POST['emplacement']) == 0) {
		Bataille::getBatiment()->getUnBatiment($_POST['nom_batiment']." addon", 0);
	}

	$arr = \modules\bataille\app\controller\Bataille::getValues();