<?php
	/**
	 * ce script permet de désactiver le mode vacances après 40 jours si le joueur ne s'est pas
	 * reconnecté avant
	 */
	$today = new DateTime();
	$today->sub(new DateInterval("P40D"));
	$date_supprimer = $today->format("Y-m-d h:i:s");
	
	$query = $dbc->select()->from("_bataille_infos_player")
		->where("last_connexion", "<=", $date_supprimer, "AND")
		->where("mode_vacances", "=", 1, "AND")
		->where("abandon", "!=", 1)
		->get();
	
	if (count($query) > 0) {
		foreach ($query as $obj) {
			$dbc->update("mode_vacances", 0)->from("_bataille_infos_player")
				->where("ID_identite", "=", $obj->ID_identite)->set();
		}
	}