<?php
	/**
	 * Ce script permet de terminer les offres de marché en cours et de livrer les ressources
	 */
	$query = $dbc->select("ID_base")->from("_bataille_marche_transport")->get();
	
	if (count($query) > 0) {
		foreach ($query as $obj) {
			$_SESSION['id_base'] = $obj->ID_base;
			
			$marche = new \modules\bataille\app\controller\Marche();
		}
	}
	