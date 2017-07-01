<?php
	/**
	 * Ce script permet de terminer les recrutements
	 */
	$query = $dbc->select("ID_base")->from("_bataille_missions_cours")->get();
	
	if (count($query) > 0) {
		foreach ($query as $obj) {
			$query1 = $dbc->select("ID_identite")->from("_bataille_base")->where("ID_base", "=", $obj->ID_base)->get();
			foreach ($query1 as $obj1) $_SESSION['idlogin'.CLEF_SITE] = $obj1->ID_identite;
			
			$_SESSION['id_base'] = $obj->ID_base;
			
			$missions = new \modules\bataille\app\controller\MissionsAleatoire();
			$missions->setTerminerMissions();
		}
	}
	
	session_destroy($_SESSION['idlogin'.CLEF_SITE]);
	session_destroy($_SESSION['id_base']);