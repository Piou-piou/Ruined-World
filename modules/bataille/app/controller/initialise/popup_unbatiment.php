<?php
	use \modules\bataille\app\controller\Bataille;

	Bataille::getIdBase();

	if (Bataille::getBatiment()->getUnBatiment($_POST['nom_batiment'], $_POST['emplacement']) == 0) {
		$max_level = true;
	}
	else {
		$max_level = false;
	}