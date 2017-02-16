<?php
	$faction = new \modules\bataille\app\controller\Faction();
	$faction->getFactionPlayer();
	
	$faction->setRenvoyerMembre($_POST['id_identite']);