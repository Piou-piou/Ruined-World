<?php
	//recuperation des messages supprimés
	$query = $dbc->select()->from("_messagerie_boite_reception, _messagerie_message")
	->where("_messagerie_boite_reception.supprimer", "=", 1, "AND")
	->where("_messagerie_boite_reception.ID_message", "=", "_messagerie_message.ID_message", "", true)
	->get();
	
	if (count($query) > 0) {
		echo("yes");
	}
	else {
		echo("no");
	}