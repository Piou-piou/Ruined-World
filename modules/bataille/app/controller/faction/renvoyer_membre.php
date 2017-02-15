<?php
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	if ($faction->setRenvoyerMembre($_POST['id_identite']) == true) {
		
	}