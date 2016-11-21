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
				
				//récupération de la posisiotn de la base du joueur + la base sur laquelle on a cliqué
				$pos_bases = Bataille::getDureeTrajet($id_base);
				$base_joueur = $pos_bases[0];
				$base_autre = $pos_bases[1];
				
				//calcul des distances séparant les deux bases en x et y
				//cette dstance sera multipliée par 10 sur x et y puis ajoutée pour avoir le temps du trajte en seconde
				$calc_x = abs($base_joueur['posx']-$base_autre['posx']);
				$calc_y = abs($base_joueur['posy']-$base_autre['posy']);
				
				$temps_voyage = ($calc_x*15)+($calc_y*15);
				
				echo $temps_voyage;
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
						"ma_base" => $ma_base
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
			$dbc = App::getDb();

			$query = $dbc->select()->from("_bataille_map")->where("ID_map", "=", 1)->get();

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