<?php
	/**
	 * Ce script permet d'actualiser les ressources des bases
	 */
	$query = $dbc->select("ID_base, ID_identite")->from("_bataille_base")->get();
	
	if (count($query) > 0) {
		foreach ($query as $obj) {
			$_SESSION['idlogin'.CLEF_SITE] = $obj->ID_identite;
			$_SESSION['id_base'] = $obj->ID_base;
			
			new \modules\bataille\app\controller\Ressource($obj->ID_base);
		}
	}
	
	session_destroy($_SESSION['idlogin'.CLEF_SITE]);
	session_destroy($_SESSION['id_base']);