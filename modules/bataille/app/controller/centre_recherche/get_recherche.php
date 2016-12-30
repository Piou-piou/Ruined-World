<?php
	$centre_recherche = new \modules\bataille\app\controller\CentreRecherche();

	\modules\bataille\app\controller\Bataille::getCentreRecherche()->getRecherche();

	$arr = \modules\bataille\app\controller\Bataille::getValues();