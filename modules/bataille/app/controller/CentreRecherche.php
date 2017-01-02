<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\functions\DateHeure;
	use core\HTML\flashmessage\FlashMessage;

	class CentreRecherche {
		private $coef_centre;
		private $recherche;
		private $type;
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select("coef_centre_recherche")->from("configuration")->where("ID_configuration", "=", 1)->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) $this->coef_centre = $obj->coef_centre_recherche;
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
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

		/**
		 * @param $cout
		 * @param $niveau_recherche
		 * @return array
		 * fonction qui renvoi le cout d'une recherche
		 */
		private function getCoutRecherche($cout, $niveau_recherche) {
			return [
				"eau" => $cout["eau"] * ($this->coef_centre * $niveau_recherche),
				"electricite" => $cout["electricite"] * ($this->coef_centre * $niveau_recherche),
				"fer" => $cout["fer"] * ($this->coef_centre * $niveau_recherche),
				"fuel" => $cout["fuel"] * ($this->coef_centre * $niveau_recherche)
			];
		}

		/**
		 * @param $temps
		 * @param int $niveau
		 * @return floatfonction qui renvoi le temps qu'il faut pour effectuer une recherche
		 */
		private function getTempsRecherche($temps, $niveau = 0) {
			$pourcent = ($temps*Bataille::getBatiment()->getNiveauBatiment("centre_recherche")/100);

			if ($niveau == 0) {
				return round($temps-$pourcent);;
			}

			return round(($temps * ($this->coef_centre * $niveau))-$pourcent);
		}

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

		/**
		 * fonction qui renvoi toutes les recherches effectuées ou non dans un tableau
		 * (ne renvoi que celle que l'on peut faire en fonction du niveau du centre)
		 */
		public function getAllRecherche() {
			$dbc1 = Bataille::getDb();

			//avant de récupérer toutes les recherches, on finit au cas celle en court
			if ($this->getRecherche() == false) {
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
		}

		/**
		 * @return bool
		 * fonction qui renvoi un tableau contenant la recherche en cours si celle-ci n'est pas finie
		 * sinon elle appelle la fonction setTerminerRecherche
		 */
		public function getRecherche() {
			$dbc = App::getDb();

			$query = $dbc->select()->from("_bataille_recherche")->where("ID_base", "=", Bataille::getIdBase())->get();

			if ((is_array($query)) && (count($query) > 0)) {
				$today = Bataille::getToday();

				foreach ($query as $obj) {
					$this->recherche = $obj->recherche;
					$this->type = $obj->type;

					if ($obj->date_fin-$today <= 0) {
						$this->setTerminerRecherche($obj->ID_recherche);

						return false;
					}
					else {
						$recherche = [
							"recherche" => $obj->recherche,
							"type" => $obj->type,
							"date_fin_recherche" => $obj->date_fin-$today,
							"id_recherche" => $obj->ID_recherche
						];
					}
				}

				Bataille::setValues(["recherche" => $recherche]);

				return true;
			}

			return false;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		public function setCommencerRecherche($recherche, $type) {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();

			//on test si il n'y a pas déjà une recherche en cours
			if ($this->getRecherche() == true) {
				FlashMessage::setFlash("Une recherche est déjà en cours, merci d'attendre la fin de celle-ci");
				return false;
			}

			//on récupère la recherche dans notre base savoir si on l'a déjà recherchée pour avoir son lvl
			$niveau_recherche = $this->getNiveauRecherche($recherche, $type);

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
					$cout = unserialize($obj->cout);
					$temps_recherche = $obj->temps_recherche;
				}
			}

			if ($niveau_recherche > 0) {
				$cout = $this->getCoutRecherche($cout, $niveau_recherche);
				$temps_recherche = $this->getTempsRecherche($temps_recherche, $niveau_recherche);
			}

			//on test si assez de ressources pour effectuer la recherche
			$eau = Bataille::getTestAssezRessourceBase("eau", $cout["eau"]);
			$electricite = Bataille::getTestAssezRessourceBase("electricite", $cout["electricite"]);
			$fer = Bataille::getTestAssezRessourceBase("fer", $cout["fer"]);
			$fuel = Bataille::getTestAssezRessourceBase("fuel", $cout["fuel"]);


			if (($eau["class"] || $electricite["class"] || $fer["class"] || $fuel["class"]) == "rouge" ) {
				FlashMessage::setFlash("Pas assez de ressources pour effectuer cette recherche");
				return false;
			}
			else {
				//on retire les ressources
				Bataille::getRessource()->setUpdateRessource($cout["eau"], $cout["electricite"], $cout["fer"], $cout["fuel"], 0, "-");

				$date_fin = Bataille::getToday()+$temps_recherche;

				$dbc->insert("recherche", $recherche)
					->insert("type", $type)
					->insert("date_fin", $date_fin)
					->insert("ID_base", Bataille::getIdBase())
					->into("_bataille_recherche")
					->set();

				return true;
			}
		}

		private function setTerminerRecherche($id_recherche) {
			$dbc = App::getDb();
			$niveau_recherche = $this->getNiveauRecherche($this->recherche, $this->type);

			if ($niveau_recherche == 0) {
				$dbc->insert("recherche", $this->recherche)
					->insert("type", $this->type)
					->insert("niveau", 1)
					->insert("ID_base", Bataille::getIdBase())
					->into("_bataille_centre_recherche")
					->set();
			}
			else {
				$dbc->update("niveau", $niveau_recherche+1)
					->from("_bataille_centre_recherche")
					->where("recherche", "=", $this->recherche, "AND")
					->where("type", "=", $this->type, "AND")
					->where("ID_base", "=", Bataille::getIdBase())
					->set();
			}

			$dbc->delete()->from("_bataille_recherche")->where("ID_recherche", "=", $id_recherche, "AND")
				->where("ID_base", "=", Bataille::getIdBase())
				->del();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}