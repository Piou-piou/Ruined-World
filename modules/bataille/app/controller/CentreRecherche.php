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
		private $coef_centre;
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select("coef_centre_recherche")->from("configuration")->where("ID_configuration", "=", 1)->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) $this->coef_centre = $obj->coef_centre_recherche;
			}

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

			$query = $dbc1->select()->from("recherche")
				->where("niveau_centre", "<=", Bataille::getBatiment()->getNiveauBatiment("centre_recherche"))
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$all_recherche[] = [
						"recherche" => $obj->recherche,
						"type" => $obj->type,
						"cout" => unserialize($obj->cout)
					];
				}
			}

			$count = count($all_recherche);

			for ($i=0 ; $i<$count ; $i++) {
				if ((in_array($all_recherche[$i]["recherche"], $recherche_base[$i]))) {
					$niveau = $recherche_base[$i]["niveau"];
					if ($niveau == 1) $this->coef_centre = 1;

					$all_recherche[$i]["cout"] = [
						"eau" => $all_recherche[$i]["cout"]["eau"]*($this->coef_centre*$niveau),
						"electricite" => $all_recherche[$i]["cout"]["electricite"]*($this->coef_centre*$niveau),
						"fer" => $all_recherche[$i]["cout"]["fer"]*($this->coef_centre*$niveau),
						"fuel" => $all_recherche[$i]["cout"]["fuel"]*($this->coef_centre*$niveau)
					];
					$ameliorer = true;
				}
				else {
					$ameliorer = false;
				}

				$centre_recherche[] = [
					"recherche" => $all_recherche[$i]["recherche"],
					"type" => $all_recherche[$i]["type"],
					"cout" => $all_recherche[$i]["cout"],
					"ameliorer" => $ameliorer
				];
			}

			Bataille::setValues(["centre_recherche" => $centre_recherche]);
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