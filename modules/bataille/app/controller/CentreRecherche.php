<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\functions\DateHeure;

	class CentreRecherche {
		private $coef_centre;
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select("coef_centre_recherche")->from("configuration")->where("ID_configuration", "=", 1)->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) $this->coef_centre = $obj->coef_centre_recherche;
			}

			$query = $dbc1->select()->from("recherche")
				->where("niveau_centre", "<=", Bataille::getBatiment()->getNiveauBatiment("centre_recherche"))
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$niveau = $this->getNiveauRecherche($obj->recherche, $obj->type);
					$niveau_recherche = $niveau;

					$cout = unserialize($obj->cout);
					$temps_recherche = $obj->temps_recherche;

					//si niveau == 0 ca veut dire que la recherche n'a pas encore été effectuée dans la base
					if ($niveau > 0) {
						$cout = [
							"eau" => $cout["eau"] * ($this->coef_centre * $niveau),
							"electricite" => $cout["electricite"] * ($this->coef_centre * $niveau),
							"fer" => $cout["fer"] * ($this->coef_centre * $niveau),
							"fuel" => $cout["fuel"] * ($this->coef_centre * $niveau)
						];

						$temps_recherche = $temps_recherche * ($this->coef_centre * $niveau);
					}
					else {
						$niveau_recherche = 1;
					}

					$recherhce[] = [
						"recherche" => $obj->recherche,
						"type" => $obj->type,
						"niveau" => $niveau,
						"cout" => $cout,
						"temps_recherche" => DateHeure::Secondeenheure($temps_recherche),
						"special" => Bataille::getUnite()->getCaracteristiqueUnite($obj->recherche, $niveau_recherche, $obj->type),
						"coef_amelioration" => Bataille::getParam("coef_niveau_unite")
					];
				}
			}

			Bataille::setValues(["centre_recherche" => $recherhce]);
		}

		/**
		 * @param $recherche
		 * @param $type
		 * @return int
		 * fonction qui va cehrcher le niveau de la recherche actuelle
		 * renvoi 0 si elle n'a pas été trouvée
		 */
		private function getNiveauRecherche($recherche, $type) {
			$dbc = App::getDb();

			$query = $dbc->select("niveau")
				->from("_bataille_centre_recherche")
				->where("ID_base", "=", Bataille::getIdBase(), "AND")
				->where("recherche", "=", $recherche, "AND")
				->where("type", "=", $type)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					return $obj->niveau;
				}
			}

			return 0;
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