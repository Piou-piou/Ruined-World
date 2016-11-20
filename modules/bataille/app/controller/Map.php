<?php

	namespace modules\bataille\app\controller;

	use core\App;

	class Map {



		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc = App::getDb();

			$this->getParametres();

			$query = $dbc->select("_bataille_base.nom_base")
				->select("_bataille_base.points")
				->select("_bataille_base.posx")
				->select("_bataille_base.posy")
				->select("_bataille_base.ID_identite")
				->select("identite.pseudo")
				->from("identite")
				->from("_bataille_base")
				->where("_bataille_base.ID_identite", "=", "identite.ID_identite", "", true)
				->get();

			foreach ($query as $obj) {
				$ma_base = 0;
				if ($obj->ID_identite == 1) {
					$ma_base = "ma-base";
				}

				$map[] = [
					"nom_base" => $obj->nom_base,
					"points" => $obj->points,
					"posx" => $obj->posx,
					"posy" => $obj->posy,
					"id_identite" => $obj->ID_identite,
					"pseudo" => $obj->pseudo,
					"ma_base" => $ma_base
				];
			}

			Bataille::setValues(["map" => $map]);
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