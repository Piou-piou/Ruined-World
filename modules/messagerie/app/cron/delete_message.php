<?php
	/**
	 * script qui permet de supprimer tous les messages mit en archive depuis plus de 15jours
	 */
	//today
	$today = new DateTime();
	$today->sub(new DateInterval("P15D"));
	$date_supprimer = $today->format("Y-m-d h:i:s");
	
	//recuperation des messages supprimés
	$query = $dbc->select()->from("_messagerie_message")
	->where("_messagerie_message.date", "<=", $date_supprimer)
	->get();
	
	if (count($query) > 0) {
		foreach ($query as $obj) {
			$query1 = $dbc->select()->from("_messagerie_boite_reception")
				->where("ID_message", "=", $obj->ID_message)->get();
			
			$supprimer = 0;
			
			foreach ($query1 as $obj1) {
				if ($obj1->supprimer == 1) {
					$supprimer += 1;
				}
			}
			
			if ($supprimer == count($query1)) {
				$dbc->delete()->from("_messagerie_message")->where("ID_message", "=", $obj->ID_message)->del();
				$dbc->delete()->from("_messagerie_boite_reception")->where("ID_message", "=", $obj->ID_message)->del();
			}
		}
	}