<?php
	/**
	 * Created by PhpStorm.
	 * User: anthony
	 * Date: 08/12/2016
	 * Time: 20:44
	 */
	
	namespace modules\bataille\app\controller;
	
	
	use core\App;

	class CentreRecherche {
		
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc = App::getDb();

			$query = $dbc->select()->from("_bataille_centre_recherche")->where("ID_base", "=", Bataille::getIdBase())->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$recherche_base[] = [
						"recherche" => $obj->recherche,
						"niveau" => $obj->niveau,
						"type" => $obj->type
					];
				}
			}

			$query = $dbc->select()->from("recherche")->where("niveau", ">=", Bataille::getBatiment()->getNiveauBatiment("centre_recherche"))->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$all_recherche[] = [
						"recherche" => $obj->recherche,
						"type" => $obj->type,
						"cout" => unserialize($obj->cout)
					];
				}
			}

			$count = cout($all_recherche);

			for ($i=0 ; $i<$count ; $i++) {
				if ((in_array($all_recherche[$i], $recherche_base))) {
					echo("améliorer<br>");
				}
				else {
					echo("rechercher<br>");
				}
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * @param $type
		 * @return array|int
		 * permet de renvoyer toutes es recherches déjà effectuées pour notre base en fonction
		 * d'un type donné
		 */
		public function getAllRechercheType($type) {
			$dbc = App::getDb();

			$query = $dbc->select()->from("_bataille_centre_recherche")->where("type", "=", $type)->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$recherche[] = [
						"niveau" => $obj->niveau,
						"recherche" => $obj->recherche
					];
				}

				return $recherche;
			}

			return 0;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}