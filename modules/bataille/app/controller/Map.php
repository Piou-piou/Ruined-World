<?php

	namespace modules\bataille\app\controller;

	use core\App;

	class Map {



		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct($id_base = null) {
			$dbc = App::getDb();
			
			if ($id_base == null) {
				$this->getParametres();
				
				$query = $dbc->select("_bataille_base.nom_base")
					->select("_bataille_base.points")
					->select("_bataille_base.posx")
					->select("_bataille_base.posy")
					->select("_bataille_base.ID_base")
					->select("identite.pseudo")
					->select("identite.ID_identite")
					->from("identite")
					->from("_bataille_base")
					->where("_bataille_base.ID_identite", "=", "identite.ID_identite", "", true)
					->get();
			}
			else {
				$query = $dbc->select("_bataille_base.nom_base")
					->select("_bataille_base.points")
					->select("_bataille_base.posx")
					->select("_bataille_base.posy")
					->select("_bataille_base.ID_base")
					->select("identite.ID_identite")
					->select("identite.pseudo")
					->from("identite")
					->from("_bataille_base")
					->where("_bataille_base.ID_base", "=", $id_base, "AND")
					->where("_bataille_base.ID_identite", "=", "identite.ID_identite", "", true)
					->get();

				$temps_trajet = gmdate("H:i:s", Bataille::getDureeTrajet($id_base));
			}
			
			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$ma_base = "";
					if ($obj->ID_identite == 1) {
						$ma_base = "ma-base";
					}
					
					$map[] = [
						"nom_base" => $obj->nom_base,
						"points" => $obj->points,
						"posx" => $obj->posx,
						"posy" => $obj->posy,
						"id_base" => $obj->ID_base,
						"id_identite" => $obj->ID_identite,
						"pseudo" => $obj->pseudo,
						"ma_base" => $ma_base,
						"temps_trajet" => $temps_trajet
					];
				}
				
				Bataille::setValues(["map" => $map]);
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui sert à récupérer les parametres de la map
		 */
		private function getParametres() {
			$dbc = Bataille::getDb();

			$query = $dbc->select()->from("map")->where("ID_map", "=", 1)->get();

			foreach ($query as $obj) {
				Bataille::setValues([
					"largeur_map" => $obj->largeur,
					"hauteur_map" => $obj->hauteur
				]);
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}