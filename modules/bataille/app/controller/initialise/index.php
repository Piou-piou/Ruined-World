<?php
	require_once("test_connexion.php");
	use modules\bataille\app\controller\Bataille;

	//test si on a une base dans l'url
	if (isset($_POST['base'])) {
		$_SESSION['id_base'] = $_POST['base'];
	}
	else {
		$_SESSION['id_base'] = Bataille::getFirstBase();
	}

	Bataille::getBatiment()->getConstruction();

	Bataille::getBase()->getMaBase();

	Bataille::getNation();

	$marche = new \modules\bataille\app\controller\Marche();

	$arr = Bataille::getValues();