<?php
	use modules\bataille\app\controller\Bataille;

	$_SESSION['id_base'] = 1;
	$_SESSION['idlogin'.CLEF_SITE] = 1;

	Bataille::getBatiment()->getConstruction();

	Bataille::getBase()->getMaBase();

	Bataille::getNationBase();

	$marche = new \modules\bataille\app\controller\Marche();

	$arr = Bataille::getValues();