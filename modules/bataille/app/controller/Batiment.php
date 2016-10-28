<?php
	namespace modules\bataille\app\controller;
	use core\App;
	use core\functions\ChaineCaractere;
	use core\HTML\flashmessage\FlashMessage;
	use Nette\Utils\DateTime;

	class Batiment {
		//pour quand on recup un batiment
		private $nom_batiment;
		private $nom_batiment_sql;
		private $niveau_batiment;
		private $temps_construction;
		private $ressource_construire;
		private $id_batiment;

		private $info_batiment;

		//pour les constructions
		private $nom_batiment_construction;
		private $date_fin_construction;
		private $niveau_batiment_construction;

		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {

		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getNomBatiment() {
			return $this->nom_batiment;
		}
		public function getNomBatimentSql() {
			return $this->nom_batiment_sql;
		}
		public function getNiveau() {
			return $this->niveau_batiment;
		}
		public function getTempsConstruction() {
			return $this->temps_construction;
		}
		public function getRessourceConstruire() {
			return $this->ressource_construire;
		}
		public function getInfoBatiment(){
		    return $this->info_batiment;
		}

		public function getNomBatimentConstruction() {
			return $this->nom_batiment_construction;
		}
		public function getDateFinConstruction() {
			return $this->date_fin_construction;
		}
		public function getNiveauBatimentConstruction() {
			return $this->niveau_batiment_construction;
		}

		/**
		 * @param $nom_batiment
		 * @return int
		 * pour recuperer le niveau d'un batiment
		 */
		private function getNiveauBatiment($nom_batiment_sql) {
			$dbc = App::getDb();

			$query = $dbc->select("niveau")
				->select("construction")
				->from("_bataille_batiment")
				->where("nom_batiment_sql", "=", $nom_batiment_sql, "AND")
				->where("ID_base", "=", Bataille::getIdBase())
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					if ($obj->construction == 1) {
						return $obj->niveau-1;
					}

					return $obj->niveau;
				}
			}
			else {
				return 0;
			}
		}

		/**
		 * @param $ressource
		 * @return int
		 * recuperation de la production de chaque ressource en fonction du lvl des batiments + recup prod addon
		 */
		public function getProduction($ressource) {
			$dbc1 = Bataille::getDb();

			if ($ressource == "eau") $nom_batiment = "centrale_eau";
			if ($ressource == "electricite") $nom_batiment = "centrale_electrique";
			if ($ressource == "fuel") $nom_batiment = "station_pompage_fuel";
			if ($ressource == "fer") $nom_batiment = "station_forage";

			$niveau = $this->getNiveauBatiment($nom_batiment);

			if ($niveau > 0) {
				$query = $dbc1->select("production")->from("$nom_batiment")->where("ID_".$nom_batiment, "=", $niveau)->get();

				if ((is_array($query)) && (count($query) > 0)) {
					foreach ($query as $obj) {
						$prod = $obj->production;
					}

					$query = $dbc1->select("production")
						->from($nom_batiment."_addon")
						->where("ID_".$nom_batiment."_addon", "=", $this->getNiveauBatiment($nom_batiment."_addon"))
						->get();

					if ((is_array($query)) && (count($query) > 0)) {
						foreach ($query as $obj) {
							$prod_addon = $obj->production;
						}
					}
					else {
						$prod_addon = 0;
					}

					return $prod + $prod_addon;
				}
			}
			else {
				return 20;
			}
		}

		/**
		 * @return int
		 * fonction qui retourne le stockage de l'entrepot
		 */
		public function getStockageEntrepot() {
			$dbc1 = Bataille::getDb();

			$niveau = $this->getNiveauBatiment("entrepot");

			if ($niveau > 0) {
				$query = $dbc1->select("stockage")->from("entrepot")->where("ID_entrepot", "=", $niveau)->get();

				if ((is_array($query)) && (count($query) > 0)){
					foreach ($query as $obj) {
						return $obj->stockage;
					}
				}
			}
			else {
				return 1000;
			}
		}

		/**
		 * permet de récupérer toutes les infos d'un batiment dans la popup
		 * @param $nom_batiment
		 * @param $emplacement
		 */
		public function getUnBatiment($nom_batiment, $emplacement) {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();

			$construction = $this->getTestBatimentConstruction($nom_batiment);

			//recuperation des infos du batiment
			$query = $dbc->select()
				->from("_bataille_batiment")
				->where("nom_batiment", "=", $construction[0], "AND")
				->where("emplacement", "=", $emplacement, "AND")
				->where("ID_base", "=", Bataille::getIdBase())
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->nom_batiment_sql = $obj->nom_batiment_sql;
					$this->niveau_batiment = $obj->niveau;
					$this->id_batiment = $obj->ID_batiment;
				}

				if (($construction[1] == true) && ($this->niveau_batiment > 1)) {
					$this->niveau_batiment = $this->niveau_batiment+1;
				}

				return $this->getInfoUpgradeBatiment();
			}
			else {
				//on test voir si le bat est au niveau max et si il peut avoir un addon
				if (ChaineCaractere::FindInString($nom_batiment, "addon")) {
					$query = $dbc1->select("nom_table")->from("liste_batiment")->where("nom", "=", $nom_batiment)->get();

					if ((is_array($query)) && (count($query) > 0)) {
						foreach ($query as $obj) {
							$this->nom_batiment_sql = $obj->nom_table;
						}

						$this->niveau_batiment = 0;

						return $this->getInfoUpgradeBatiment();
					}
					else {
						return 0;
					}
				}


				return 0;
			}
		}

		/**
		 * permet de savoir si le batiment produit bien des ressoures
		 * @return array|bool
		 */
		public function getTestBatimentProductionRessource() {
			$dbc1 = Bataille::getDb();
			$batiment_production = [];

			$query = $dbc1->select("nom")->from("liste_batiment")->where("type", "=", "ressource")->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$batiment_production[] = $obj->nom;
				}

				return $batiment_production;
			}

			return $batiment_production;
		}

		/**
		 * pour récupérer la construction en cours dans la base
		 */
		public function getConstruction() {
			$dbc = App::getDb();

			$today = new DateTime();
			$today = $today->getTimestamp();

			$query = $dbc->select()
				->from("_bataille_construction")
				->from("_bataille_batiment")
				->where("_bataille_construction.ID_base", "=", Bataille::getIdBase(), "AND")
				->where("_bataille_construction.ID_batiment", "=", "_bataille_batiment.ID_batiment", "AND", true)
				->where("_bataille_construction.ID_base", "=", "_bataille_batiment.ID_base", "", true)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->nom_batiment_construction = $obj->nom_batiment;
					$this->date_fin_construction = $obj->date_fin;
					$this->niveau_batiment_construction = $obj->niveau;
					$id_batiment = $obj->ID_batiment;
				}

				if ($this->date_fin_construction-$today <= 0) {
					$this->setTerminerConstruction($id_batiment);
				}
				else {
					$this->date_fin_construction = $this->date_fin_construction-$today;
				}

				return 1;
			}

			return 0;
		}

		/**
		 * pour récupérer la liste des batiments qu'il est possible de construire
		 */
		public function getBatimentAConstruire() {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();
			$batiment_construit = [];
			$batiment_construire = [];

			//recuperation des batiments deja construit dans la base
			$query = $dbc->select("nom_batiment_sql")
				->select("nom_batiment")
				->select("niveau")
				->from("_bataille_batiment")
				->where("ID_base", "=", Bataille::getIdBase())
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$batiment_construit[] = $obj->nom_batiment_sql;
				}
			}

			//recuperation de la liste complete des batiments
			$query = $dbc1->select("nom_table")->select("nom")->from("liste_batiment")->where("actif", "=", 1)->get();
			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$all_batiment[] = $obj->nom_table;
					$all_batiment_nom[] = $obj->nom;
				}

				$c_all_batiment = count($all_batiment);
			}

			//boucle qui recupere en tableau le champ pour_construire d'un batiment
			//et compare la liste des batiments qu'il faut pour construire le batiment
			//a ceux qui sont deja construit dans la base
			//si tous les batments qu'il faut son batis on autorise la construction du batiment
			for ($i=0 ; $i<$c_all_batiment ; $i++) {
				if (!in_array($all_batiment[$i], $batiment_construit)) {
					$query = $dbc1->select("pour_construire")
						->select("temps_construction")
						->from($all_batiment[$i])
						->where("ID_".$all_batiment[$i], "=", 1)
						->get();

					if ((is_array($query)) && (count($query) > 0)) {
						foreach ($query as $obj) {
							if ($obj->pour_construire != null) {
								$pour_construire = unserialize($obj->pour_construire);
							}
							else {
								$pour_construire = [];
							}


							$temps_construction = gmdate("H:i:s", $obj->temps_construction);
						}
					}


					if (count($pour_construire) == 1) {
						if (in_array($pour_construire[0][1], $batiment_construit)) {
							if ($pour_construire[0][2] <= $this->getNiveauBatiment($pour_construire[0][1])) {
								$ressource = $this->getRessourceConstruireBatiment($all_batiment[$i], 0);

								$batiment_construire[] = [
									$all_batiment[$i],
									$all_batiment_nom[$i],
									$ressource,
									$temps_construction
								];
							}
						}
					}
					else if (count($pour_construire) > 1) {
						//TODO si besoin de plus d'un seul batiment pour etre construit
					}
					else {
						$ressource = $this->getRessourceConstruireBatiment($all_batiment[$i], 0);

						$batiment_construire[] = [
							$all_batiment[$i],
							$all_batiment_nom[$i],
							$ressource,
							$temps_construction
						];
					}
				}
			}

			return $batiment_construire;
		}

		/**
		 * @param $nom_batiment_sql
		 * @param $niveau
		 * @return array
		 * recuperation des ressources nécéssaire pour construire le batiment
		 */
		private function getRessourceConstruireBatiment($nom_batiment_sql, $niveau) {
			$dbc1 = Bataille::getDb();

			$niveau = $niveau+1;

			$query = $dbc1->select("ressource_construire")->from($nom_batiment_sql)->where("ID_".$nom_batiment_sql, "=", $niveau)->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$ressource = $obj->ressource_construire;
				}
				$ressource = explode(", ", $ressource);

				//on test si assez de ressources dans la base
				//fer
				$ressource[0] = Bataille::getTestAssezRessourceBase("fer", $ressource[0]);
				//fuel
				$ressource[1] = Bataille::getTestAssezRessourceBase("fuel", $ressource[1]);
				//eau
				$ressource[2] = Bataille::getTestAssezRessourceBase("eau", $ressource[2]);
				//electricite
				$ressource[3] = Bataille::getTestAssezRessourceBase("electricite", $ressource[3]);

				return $ressource;
			}
		}

		/**
		 * fonction qui renvoi un tableau avec le nom du batiment sans (en construction)
		 * + true pour dire que le batiment est en construction
		 *
		 * @param $nom_batiment
		 * @return array
		 */
		private function getTestBatimentConstruction($nom_batiment) {
			if (ChaineCaractere::FindInString($nom_batiment, " en construction") == true) {
				return [substr($nom_batiment, 0, (0-strlen(" en construction"))), true];
			}

			return [$nom_batiment, false];
		}

		/**
		 * fonction qui renvoi les informations pour augmenter le niveau d'un batiment
		 * ou la création d'un addon sur ce batiment
		 * @return int
		 */
		private function getInfoUpgradeBatiment() {
			$dbc1 = Bataille::getDb();

			//récupération du temps et des ressources pour construire
			$query = $dbc1->select()->from($this->nom_batiment_sql)->where("ID_".$this->nom_batiment_sql, "=", $this->niveau_batiment+1)->get();

			//si on a quelque chose cela veut dire qu'on est pas encore au lvl max du batiment
			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->ressource_construire = $this->getRessourceConstruireBatiment($this->nom_batiment_sql, $this->niveau_batiment);
					$this->temps_construction = gmdate("H:i:s", $obj->temps_construction);
				}

				//récupération des éléments particulier à un batiment
				if ($this->nom_batiment_sql == "entrepot") {
					$query = $dbc1->select("stockage")->from("entrepot")->where("ID_entrepot", "=", $this->niveau_batiment+1)->get();

					if ((is_array($query)) && (count($query) > 0)){
						foreach ($query as $obj) {
							$this->info_batiment = "Capacité de l'entrepôt au prochain niveau : ". $obj->stockage;
						}
					}
				}

				return 1;
			}

			return 0;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui initialise la construction d'un batiment
		 * @param $nom_batiment
		 * @param $nom_batiment_sql
		 * @param $emplacement
		 */
		public function setCommencerConstruireBatiment($nom_batiment, $nom_batiment_sql, $emplacement) {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();

			if (ChaineCaractere::FindInString($nom_batiment, "addon")) {
				$emplacement = 0;
			}

			if ($this->getConstruction() == 0) {
				$un_batiment = $this->getUnBatiment($nom_batiment, $emplacement);

				//on test si assez de ressrouce pour construire le batiment
				if ($un_batiment == 0) {
					$ressource = $this->getRessourceConstruireBatiment($nom_batiment_sql, 0);
					$this->nom_batiment_sql = $nom_batiment_sql;
					$this->niveau_batiment = 0;
				}
				else {
					//si c'est le lvl 0 de l'addon
					if ($this->niveau_batiment == 0) {
						$un_batiment = 0;
					}
					$ressource = $this->getRessourceConstruireBatiment($this->nom_batiment_sql, $this->niveau_batiment);
				}

				//si pas assez de ressource
				if (in_array("rouge", $ressource[0])) {
					FlashMessage::setFlash("Pas assez de ressources pour construire ce batiment");
				}
				else {
					//recuperation du temps de construction
					$query = $dbc1->select("ressource_construire")
						->select("temps_construction")
						->from($this->nom_batiment_sql)
						->where("ID_".$this->nom_batiment_sql, "=", $this->niveau_batiment+1)
						->get();

					foreach ($query as $obj) {
						$temps_construction = $obj->temps_construction;
						$ressource_construction = explode(", ", $obj->ressource_construire);
					}

					//on insere la construction dans la table batiment si new batiment
					if ($un_batiment == 0) {
						$dbc->insert("niveau", $this->niveau_batiment+1)
							->insert("emplacement", $emplacement)
							->insert("nom_batiment", $nom_batiment)
							->insert("nom_batiment_sql", $this->nom_batiment_sql)
							->insert("construction", 1)
							->insert("ID_base", Bataille::getIdBase())
							->into("_bataille_batiment")
							->set();

						$this->id_batiment = $dbc->lastInsertId();
					}
					else {
						$dbc->update("niveau", $this->niveau_batiment+1)
							->update("construction", 1)
							->from("_bataille_batiment")
							->where("ID_batiment", "=", $this->id_batiment, "AND")
							->where("ID_base", "=", Bataille::getIdBase())
							->set();
					}


					//on initialise la construction
					//recuperation de la date en seconde
					$today = new DateTime();
					$today = $today->getTimestamp();

					//date de la fin de la construction en seconde
					$fin_construction = $today+$temps_construction;

					$dbc->insert("date_fin", $fin_construction)
						->insert("emplacement_construction", $emplacement)
						->insert("ID_base", Bataille::getIdBase())
						->insert("ID_batiment", $this->id_batiment)
						->into("_bataille_construction")
						->set();

					//on retire les ressources de la base
					Bataille::getRessource()->setRetirerRessource($ressource_construction[2], $ressource_construction[3], $ressource_construction[0], $ressource_construction[1]);
				}
			}
			else {
				FlashMessage::setFlash("Un batiment est déjà en construction, vous ne pouvez pas en construire un autre !");
			}
		}

		/**
		 * fonction qui termine la construction d'un batiment
		 * @param $id_batiment
		 */
		private function setTerminerConstruction($id_batiment) {
			$dbc = App::getDb();

			//on le retire de la table construction
			$dbc->delete()
				->from("_bataille_construction")
				->where("ID_base", "=", Bataille::getIdBase(), "AND")
				->where("ID_batiment", "=", $id_batiment)
				->del();

			//on termine la construction dans la table batiment
			$dbc->update("construction", "NULL")
				->from("_bataille_batiment")
				->where("ID_base", "=", Bataille::getIdBase(), "AND")
				->where("ID_batiment", "=", $id_batiment)
				->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}