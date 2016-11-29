<?php
	require_once("test_connexion.php");

	//test si on a une base dans l'url
	if (isset($_GET['base'])) {
		$_SESSION['id_base'] = $_GET['base'];
	}
	else {
		$_SESSION['id_base'] = Bataille::getFirstBase();
	}


	use modules\bataille\app\controller\Bataille;

	/*$_SESSION['id_base'] = 1;
	$_SESSION['idlogin'.CLEF_SITE] = 1;*/

	Bataille::getBatiment()->getConstruction();

	Bataille::getBase()->getMaBase();

	Bataille::getNationBase();

	$marche = new \modules\bataille\app\controller\Marche();

	$arr = Bataille::getValues();