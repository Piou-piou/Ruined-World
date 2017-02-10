<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class Faction {
		private $id_faction;
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * @return mixed
		 * fonction qui renvoi l'ID de la faction du joueur
		 */
		public function getFactionPlayer() {
			$dbc = App::getDb();
			
			$query = $dbc->select("ID_faction")->from("_bataille_infos_player")
				->where("ID_identite", "=", Bataille::getIdIdentite(), "AND")
				->where("ID_faction", ">", 0)
				->get();
			
			if ((count($query) > 0)) {
				foreach ($query as $obj) {
					$this->id_faction = $obj->ID_faction;
					$this->getInfosFaction();
				}
			}
		}
		
		/**
		 * @param null $id_faction
		 * fonction qui récupère les infos de la faction
		 */
		public function getInfosFaction($id_faction = null) {
			$dbc = App::getDb();
			
			$id_faction = $this->id_faction;
			
			$query = $dbc->select("identite.pseudo")
				->select("_bataille_faction.nom_faction")
				->select("_bataille_faction.img_profil")
				->select("_bataille_faction.description")
				->from("_bataille_faction")
				->from("identite")
				->where("_bataille_faction.ID_faction", "=", $id_faction, "AND")
				->where("_bataille_faction.ID_identite", "=", "identite.ID_identite", "", true)
				->get();
			
			if ((count($query) == 1)) {
				foreach ($query as $obj) {
					Bataille::setValues(["faction" => [
						"nom" => $obj->nom_faction,
						"description" => $obj->description,
						"url_img" => $obj->img_profil,
						"pseudo_chef" => $obj->pseudo
					]]);
				}
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}