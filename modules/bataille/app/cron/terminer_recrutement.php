<?php
	/**
	 * Ce script permet de terminer les recrutements
	 */
	$query = $dbc->select("ID_base")->from("_bataille_recrutement")->get();
	
	if (count($query) > 0) {
		foreach ($query as $obj) {
			$_SESSION['id_base'] = $obj->ID_base;
			
			$unite = new \modules\bataille\app\controller\Unite();
			$unite->getRecrutement();
		}
	}