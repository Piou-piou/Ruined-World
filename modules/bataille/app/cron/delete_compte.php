<?php
	/**
	 * ce script permet de passer un compte en inactif si pas de connexion pendant 15 jours
	 */
	$today = new DateTime();
	$today->sub(new DateInterval("P15D"));
	$date_supprimer = $today->format("Y-m-d h:i:s");
	
	$query = $dbc->select()->from("_bataille_infos_player")
		->where("last_connexion", "<=", $date_supprimer, "AND")
		->where("mode_vacances", "!=", 1, "AND")
		->where("abandon", "!=", 1)
		->get();
	
	if (count($query) > 0) {
		foreach ($query as $obj) {
			//récupération des id_bases
			$query1 = $dbc->select("ID_base")->from("_bataille_base")->where("ID_identite", "=", $obj->ID_identite)->get();
			
			foreach ($query1 as $obj1) {
				//pour chaque batiment on stoppe la construction
				$dbc->update("construction", 0)->from("_bataille_batiment")
					->where("ID_base", "=", $obj1->ID_base)->set();
				$dbc->delete()->from("_bataille_construction")->where("ID_base", "=", $obj1->ID_base)->del();
				
				
				
				//on supprime toutes les offres de marché
				$dbc->delete()->from("_bataille_marche_offre")->where("ID_base", "=", $obj1->ID_base)->del();
				
				
				
				//on supprime toues les missions + unité en missions
				$dbc->delete()->from("_bataille_missions_cours")->where("ID_base", "=", $obj1->ID_base)->del();
				$dbc->delete()->from("_bataille_mission_aleatoire")->where("ID_base", "=", $obj1->ID_base)->del();
				$dbc->update("ID_mission", 0)->from("_bataille_unite")
					->where("ID_base", "=", $obj1->ID_base)->set();
				
				
				
				//on arrete toutes les recherches + recrutement
				$dbc->delete()->from("_bataille_recherche")->where("ID_base", "=", $obj1->ID_base)->del();
				$dbc->delete()->from("_bataille_recrutement")->where("ID_base", "=", $obj1->ID_base)->del();
			
			
				//on renome la base
				$dbc->update("nom_base", "Désertée")->from("_bataille_base")->where("ID_base", "=", $obj1->ID_base)->set();
			}
			
			
			
			//on passe tous les message en lu et en supprimé
			$dbc->update("lu", 1)->update("supprimer", 1)->from("_messagerie_boite_reception")
				->where("ID_identite", "=", $obj->ID_identite)->set();
			
			
			
			//on récupère tous les joueurs de la faction soit pour la supprimer soit pour en changer le chef
			$query1 = $dbc->select("ID_identite")
				->from("_bataille_infos_player")->where("ID_faction", "=", $obj->ID_faction, "AND")
				->where("ID_identite", "!=", $obj->ID_identite, "AND")
				->where("(ID_faction IS NOT NULL AND ID_faction != 0", "", "", "", true)
				->get();
			
			if (count($query1) > 0) {
				//changer chef faction si plusieurs joueurs
				foreach ($query1 as $obj1) {
					$dbc->update("ID_identite", $obj->ID_identite)->from("_bataille_faction")
						->where("ID_faction", "=", $obj->ID_faction)->set();
				}
			}
			else {
				$dbc->delete()->from("_bataille_faction")->where("ID_identite", "=", $obj->ID_identite)->del();
			}
			
			
			
			//on retire le joueur de sa faction + ajout abandon
			$dbc->update("ID_faction", 0)->update("rang_faction", "")->update("abandon", 1)
				->from("_bataille_infos_player")->where("ID_identite", "=", $obj->ID_identite)->set();
			
			
			
			//on augmente le nombre de déserteur de +1
			$query1 = $dbc->select("nombre")->from("_bataille_deserteur")
				->where("ID_deserteur", "=", 1)->get();
			
			foreach ($query1 as $obj1) $deserteur = $obj1->nombre+1;
			
			$dbc->update("nombre", $deserteur)->from("_bataille_deserteur")
				->where("ID_deserteur", "=", 1)->set();
			
			//on change le pseudo du joueur en question
			$dbc->update("pseudo", "deserteur-".$deserteur)
				->update("nom", "deserteur-".$deserteur)
				->update("prenom", "deserteur-".$deserteur)
				->update("mail", "deserteur-".$deserteur)
				->update("mdp_params", "deserteur-".$deserteur)
				->from("identite")
				->where("ID_identite", "=", $obj->ID_identite)->set();
		}
	}