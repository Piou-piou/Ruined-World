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
					$temps_recherche = $this->getTempsRecherche($obj->temps_recherche);

					//si niveau == 0 ca veut dire que la recherche n'a pas encore été effectuée dans la base
					if ($niveau > 0) {
						$cout = $this->getCoutRecherche($cout, $niveau);
						$temps_recherche = $this->getTempsRecherche($temps_recherche, $niveau);
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

		private function getCoutRecherche($cout, $niveau_recherche) {
			return [
				"eau" => $cout["eau"] * ($this->coef_centre * $niveau_recherche),
				"electricite" => $cout["electricite"] * ($this->coef_centre * $niveau_recherche),
				"fer" => $cout["fer"] * ($this->coef_centre * $niveau_recherche),
				"fuel" => $cout["fuel"] * ($this->coef_centre * $niveau_recherche)
			];
		}

		private function getTempsRecherche($temps, $niveau = 0) {
			$pourcent = ($temps*Bataille::getBatiment()->getNiveauBatiment("centre_recherche")/100);

			if ($niveau == 0) {
				return round($temps-$pourcent);;
			}

			return round(($temps * ($this->coef_centre * $niveau))-$pourcent);
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
		public function setCommencerRecherche($recherche, $type) {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();
			$niveau_recherche = 0;

			//on récupère la recherche dans notre base savoir si on l'a déjà recherchée pour avoir son lvl
			$query = $dbc->select("niveau")->from("_bataille_centre_recherche")
				->where("recherche", "=", $recherche, "AND")
				->where("type", "=", $type, "AND")
				->where("ID_base", "=", Bataille::getIdBase())
				->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) $niveau_recherche = $obj->niveau;
			}

			//récupération du cout initial plus temps de recherche initial pour calculer les bon en fonction
			//du lvl du centre + du niveau actuel de la recherche
			$query = $dbc1->select("cout")
				->select("temps_recherche")
				->from("recherche")
				->where("recherche", "=", $recherche, "AND")
				->where("type", "=", $type)
				->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) {
					$cout = $obj->cout;
					$temps_recherche = $obj->temps_recherche;
				}
			}

			if ($niveau_recherche > 0) {
				$cout = $this->getCoutRecherche($cout, $niveau_recherche);
				$temps_recherche = $this->getTempsRecherche($temps_recherche, $niveau_recherche);
			}

			$date_fin = Bataille::getToday()+$temps_recherche;

			$dbc->insert("recherche", $recherche)
				->insert("type", $type)
				->insert("date_fin", $date_fin)
				->insert("ID_base", Bataille::getIdBase())
				->into("_bataille_recherche")
				->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}