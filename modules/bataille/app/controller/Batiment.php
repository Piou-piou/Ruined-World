<?php
	namespace modules\bataille\app\controller;
	use core\App;
	use core\functions\ChaineCaractere;
	use core\functions\DateHeure;
	use core\HTML\flashmessage\FlashMessage;

	class Batiment {
		//pour quand on recup un batiment
		private $nom_batiment;
		private $nom_batiment_sql;
		private $niveau_batiment;
		private $temps_construction;
		private $ressource_construire;
		private $id_batiment;

		private $info_batiment;
		private $info_batiment_next;

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
		public function getInfoBatiment() {
			return $this->info_batiment;
		}
		public function getInfoBatimentNext() {
			return $this->info_batiment_next;
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
		public function getNiveauBatiment($nom_batiment_sql, $id_base = null) {
			$dbc = App::getDb();

			if ($id_base == null) {
				$id_base = Bataille::getIdBase();
			}

			$query = $dbc->select("niveau")
				->select("construction")
				->from("_bataille_batiment")
				->where("nom_batiment_sql", "=", $nom_batiment_sql, "AND")
				->where("ID_base", "=", $id_base)
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
		 * recuperation de la production de chaque ressource en fonction du lvl des batiments
		 */
		public function getProduction($ressource, $id_base = null) {
			$dbc1 = Bataille::getDb();

			if ($id_base == null) {
				$id_base = Bataille::getIdBase();
			}

			$nom_batiment = "centrale_eau";
			if ($ressource == "electricite") $nom_batiment = "centrale_electrique";
			if ($ressource == "fuel") $nom_batiment = "station_pompage_fuel";
			if ($ressource == "fer") $nom_batiment = "station_forage";

			$niveau = $this->getNiveauBatiment($nom_batiment, $id_base);

			if ($niveau > 0) {
				$query = $dbc1->select("production")->from("$nom_batiment")->where("ID_".$nom_batiment, "=", $niveau)->get();

				if ((is_array($query)) && (count($query) > 0)) {
					foreach ($query as $obj) {
						return $obj->production;
					}
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
		public function getStockageEntrepot($id_base = null) {
			$dbc1 = Bataille::getDb();

			if ($id_base == null) {
				$id_base = Bataille::getIdBase();
			}

			$niveau = $this->getNiveauBatiment("entrepot", $id_base);

			if ($niveau > 0) {
				$query = $dbc1->select("stockage")->from("entrepot")->where("ID_entrepot", "=", $niveau)->get();

				if ((is_array($query)) && (count($query) > 0)) {
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
		 */
		public function getUnBatiment($nom_batiment) {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();

			$construction = $this->getTestBatimentConstruction($nom_batiment);

			//recuperation des infos du batiment
			$query = $dbc->select()
				->from("_bataille_batiment")
				->where("nom_batiment", "=", $construction[0], "AND")
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

				$max_level = $this->getInfoUpgradeBatiment();
			}
			else {
				$query = $dbc1->select("nom_table")
					->from("liste_batiment")
					->where("nom", "=", $nom_batiment)
					->get();

				if ((is_array($query)) && (count($query) > 0)) {
					foreach ($query as $obj) {
						$this->nom_batiment_sql = $obj->nom_table;
					}
				}

				$this->niveau_batiment = 0;
				$this->getInfoUpgradeBatiment();
				$max_level = 0;
			}

			//permet de savoir si le batiment produit bien des ressoures
			$batiment_production = [];

			$query = $dbc1->select("nom")->from("liste_batiment")->where("type", "=", "ressource")->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$batiment_production[] = $obj->nom;
				}
			}

			Bataille::setValues([
				"nom_batiment_sql" => $this->nom_batiment_sql,
				"niveau_batiment" => $this->niveau_batiment,
				"id_batiment" => $this->niveau_batiment,
				"max_level" => $max_level,
				"batiment_production" => $batiment_production
			]);

			return $max_level;
		}

		/**
		 * pour récupérer la construction en cours dans la base
		 */
		public function getConstruction() {
			$dbc = App::getDb();

			$today = Bataille::getToday();

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
					Bataille::setValues([
						"date_fin_construction" => $this->date_fin_construction-$today,
						"nom_batiment_construction" => $this->nom_batiment_construction
					]);
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
			for ($i = 0 ; $i < $c_all_batiment ; $i++) {
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

							$temps_construction = gmdate("H:i:s", round($obj->temps_construction-($obj->temps_construction*Bataille::getBatiment()->getNiveauBatiment("centre_commandement")/100)));
							$taille_batiment = $this->getTailleBatiment($all_batiment[$i]);
						}
					}


					if (count($pour_construire) == 1) {
						if (in_array($pour_construire[0][1], $batiment_construit)) {
							if ($pour_construire[0][2] <= $this->getNiveauBatiment($pour_construire[0][1])) {
								$ressource = $this->getRessourceConstruireBatiment($all_batiment[$i], 0);

								$batiment_construire[] = [
									"nom_batiment_sql" => $all_batiment[$i],
									"nom_batiment" => $all_batiment_nom[$i],
									"ressource" => $ressource,
									"temps_construction" => $temps_construction,
									"width" => $taille_batiment[0],
									"height" => $taille_batiment[1]
								];
							}
						}
					}
					else if (count($pour_construire) > 1) {
						$ok_construction = false;
						//test si tous les batiments sont construits et on le niveau nécéssaire
						for ($j = 0 ; $j < count($pour_construire) ; $j++) {
							if (in_array($pour_construire[$j][1], $batiment_construit)) {
								if ($pour_construire[$j][2] <= $this->getNiveauBatiment($pour_construire[$j][1])) {
									$ok_construction = true;
								}
								else {
									$ok_construction = false;
									break;
								}
							}
							else {
								$ok_construction = false;
								break;
							}
						}

						//si ok on affiche le batiment
						if ($ok_construction === true) {
							$ressource = $this->getRessourceConstruireBatiment($all_batiment[$i], 0);

							$batiment_construire[] = [
								"nom_batiment_sql" => $all_batiment[$i],
								"nom_batiment" => $all_batiment_nom[$i],
								"ressource" => $ressource,
								"temps_construction" => $temps_construction,
								"width" => $taille_batiment[0],
								"height" => $taille_batiment[1]
							];
						}
					}
					else {
						$ressource = $this->getRessourceConstruireBatiment($all_batiment[$i], 0);

						$batiment_construire[] = [
							"nom_batiment_sql" => $all_batiment[$i],
							"nom_batiment" => $all_batiment_nom[$i],
							"ressource" => $ressource,
							"temps_construction" => $temps_construction,
							"width" => $taille_batiment[0],
							"height" => $taille_batiment[1]
						];
					}
				}
			}
			Bataille::setValues(["batiments_construire" => $batiment_construire]);
		}
		
		/**
		 * @param $case_depart
		 * @param $nom_batiment
		 * @param $nom_batiment_sql
		 * @return false|null
		 * fonction qui test si un il y a la place de construire un batiment en fonction de sa case ou il a été laché en x et y
		 * et en fonction de sa taille et du positionnement des autres batiments
		 */
		public function getEmplacementConstructionLibre($case_depart, $nom_batiment, $nom_batiment_sql) {
			$dbc = App::getDb();

			//récupération de la taille du batiment
			$taille_batiment = $this->getTailleBatiment($nom_batiment_sql);
			$width_batiment = $taille_batiment[0];
			$height_batiment = $taille_batiment[1];

			//récupération des coordonnées de la sae de départ du batiment
			$case_depart = explode(",", $case_depart);
			$posx = $case_depart[0];
			$posy = $case_depart[1];
			$finx = $width_batiment+$posx;
			$finy = $height_batiment+$posy;

			//récupération de tous les batiments
			$query = $dbc->select("posx")
				->select("posy")
				->select("nom_batiment_sql")
				->from("_bataille_batiment")
				->where("ID_base", "=", Bataille::getIdBase())
				->get();
			
			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$taille_batiment = $this->getTailleBatiment($obj->nom_batiment_sql);
					$posx_batiment = $obj->posx;
					$posy_batiment = $obj->posy;

					$finx_batiment = $taille_batiment[0]+$posx_batiment;
					$finy_batiment = $taille_batiment[1]+$posy_batiment;
					
					for ($i = $posy ; $i < $finy ; $i++) {
						for ($j = $posx ; $j < $finx ; $j++) {
							if ((($posx++ >= $posx_batiment) && ($posx++ <= $finx_batiment)) && (($posy++ >= $posy_batiment) && ($posy++ <= $finy_batiment))) {
								FlashMessage::setFlash("Un batiment est déjà présent à cet emplacement, merci d'en choisir un autre");
								return false;
							}
						}
					}
					
					$posx = $case_depart[0];
					$posy = $case_depart[1];
				}

				//si tout est ok on commence la construction
				if ($this->setCommencerConstruireBatiment($nom_batiment, $nom_batiment_sql, $posx, $posy) === false) {
					return false;
				}
			}
		}

		/**
		 * @param $nom_batiment_sql
		 * @return array
		 * fonction qui renvoi un tableau contenant la taille et hauteur d'un batiment en
		 * fonction de son nom
		 */
		public function getTailleBatiment($nom_batiment_sql) {
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select("width")
				->select("height")
				->from("liste_batiment")
				->where("nom_table", "=", $nom_batiment_sql)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					return [$obj->width, $obj->height];
				}
			}
		}

		/**
		 * @param $nom_batiment_sql
		 * @param integer $niveau
		 * @return array
		 * recuperation des ressources nécéssaire pour construire le batiment
		 */
		private function getRessourceConstruireBatiment($nom_batiment_sql, $niveau) {
			$dbc1 = Bataille::getDb();

			$niveau = $niveau+1;

			$query = $dbc1->select("ressource_construire")->from($nom_batiment_sql)->where("ID_".$nom_batiment_sql, "=", $niveau)->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$ressource_tmp = $obj->ressource_construire;
				}
				$ressource_tmp = explode(", ", $ressource_tmp);

				//on test si assez de ressources dans la base
				//fer
				$ressource["fer"] = Bataille::getTestAssezRessourceBase("fer", $ressource_tmp[2]);
				//fuel
				$ressource["fuel"] = Bataille::getTestAssezRessourceBase("fuel", $ressource_tmp[3]);
				//eau
				$ressource["eau"] = Bataille::getTestAssezRessourceBase("eau", $ressource_tmp[0]);
				//electricite
				$ressource["electricite"] = Bataille::getTestAssezRessourceBase("electricite", $ressource_tmp[1]);

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
					$this->temps_construction = DateHeure::Secondeenheure(round($obj->temps_construction-($obj->temps_construction*Bataille::getBatiment()->getNiveauBatiment("centre_commandement")/100)));
				}

				//récupération des éléments particulier à un batiment
				$xml = simplexml_load_file(MODULEROOT.'bataille/data/batiment.xml');
				$nom_batiment_sql = $this->nom_batiment_sql;
				$champ = $xml->$nom_batiment_sql->champ;

				if (!empty($champ)) {
					//récupération de la phrase pour le niveau actuel
					$query = $dbc1->select($xml->$nom_batiment_sql->champ)
						->from($this->nom_batiment_sql)
						->where("ID_".$this->nom_batiment_sql, "=", $this->niveau_batiment)
						->get();

					if ((is_array($query)) && (count($query) > 0)) {
						foreach ($query as $obj) {
							$this->info_batiment = $xml->$nom_batiment_sql->phrase.$obj->$champ.$xml->$nom_batiment_sql->complement;
						}
					}

					//récupération de la phrase pour le niveau suivant
					$query = $dbc1->select($xml->$nom_batiment_sql->champ)
						->from($this->nom_batiment_sql)
						->where("ID_".$this->nom_batiment_sql, "=", $this->niveau_batiment+1)
						->get();

					if ((is_array($query)) && (count($query) > 0)){
						foreach ($query as $obj) {
							$this->info_batiment_next = $xml->$nom_batiment_sql->phrase_suivant.$obj->$champ.$xml->$nom_batiment_sql->complement;
						}
					}
				}
				else {
					$this->info_batiment = "";
					$this->info_batiment_next = "";
				}


				Bataille::setValues([
					"ressource" => $this->ressource_construire,
					"temps_construction" => $this->temps_construction,
					"info_batiment" => $this->info_batiment,
					"info_batiment_next" => $this->info_batiment_next,
				]);

				return 1;
			}

			return "max_level";
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui initialise la construction d'un batiment
		 * @param $nom_batiment
		 * @param $nom_batiment_sql
		 */
		public function setCommencerConstruireBatiment($nom_batiment, $nom_batiment_sql, $posx, $posy) {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();

			if ($this->getConstruction() == 0) {
				$un_batiment = $this->getUnBatiment($nom_batiment);

				//on test si assez de ressrouce pour construire le batiment
				if ($un_batiment == 0) {
					$ressource = $this->getRessourceConstruireBatiment($nom_batiment_sql, 0);
					$this->nom_batiment_sql = $nom_batiment_sql;
					$this->niveau_batiment = 0;
				}
				else {
					$ressource = $this->getRessourceConstruireBatiment($this->nom_batiment_sql, $this->niveau_batiment);
				}

				//si pas assez de ressource
				if (($ressource["eau"]["class"] || $ressource["electricite"]["class"] ||$ressource["fer"]["class"] || $ressource["fuel"]["class"]) == "rouge") {
					FlashMessage::setFlash("Pas assez de ressources pour construire ce batiment");
					return false;
				}
				else {
					//recuperation du temps de construction
					$query = $dbc1->select("ressource_construire")
						->select("temps_construction")
						->from($this->nom_batiment_sql)
						->where("ID_".$this->nom_batiment_sql, "=", $this->niveau_batiment+1)
						->get();

					foreach ($query as $obj) {
						$temps_construction = round($obj->temps_construction-($obj->temps_construction*Bataille::getBatiment()->getNiveauBatiment("centre_commandement")/100));
						$ressource_construction = explode(", ", $obj->ressource_construire);
					}

					//on insere la construction dans la table batiment si new batiment
					if ($un_batiment == 0) {
						$dbc->insert("niveau", $this->niveau_batiment+1)
							->insert("nom_batiment", $nom_batiment)
							->insert("nom_batiment_sql", $this->nom_batiment_sql)
							->insert("posx", intval($posx))
							->insert("posy", intval($posy))
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
					$today = Bataille::getToday();

					//date de la fin de la construction en seconde
					$fin_construction = $today+$temps_construction;

					$dbc->insert("date_fin", $fin_construction)
						->insert("ID_base", Bataille::getIdBase())
						->insert("ID_batiment", $this->id_batiment)
						->into("_bataille_construction")
						->set();

					//on retire les ressources de la base
					Bataille::getRessource()->setUpdateRessource($ressource_construction[2], $ressource_construction[3], $ressource_construction[0], $ressource_construction[1], 0, "-");
					echo("ok");
				}
			}
			else {
				FlashMessage::setFlash("Un batiment est déjà en construction, vous ne pouvez pas en construire un autre !");
				return false;
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
			$dbc->update("construction", 0)
				->from("_bataille_batiment")
				->where("ID_base", "=", Bataille::getIdBase(), "AND")
				->where("ID_batiment", "=", $id_batiment)
				->set();
			
			//on ajoute les points à la base
			Points::setAjouterPoints(Bataille::getIdBase(), "batiment");
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}