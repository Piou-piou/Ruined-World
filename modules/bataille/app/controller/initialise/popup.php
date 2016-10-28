<?php
	modules\bataille\app\controller\Bataille::getIdBase();

	$batiments = modules\bataille\app\controller\Bataille::getBatiment()->getBatimentAConstruire();
	$all_batiment = count($batiments);