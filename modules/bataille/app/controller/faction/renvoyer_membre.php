<?php
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	$faction->setRenvoyerMembre($_POST['id_identite']);
	
	if ($_POST['id_identite'] == \modules\bataille\app\controller\Bataille::getIdIdentite()) {
		echo("quitter");
	}