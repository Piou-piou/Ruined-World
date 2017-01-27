<?php
	
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\functions\DateHeure;
	use core\HTML\flashmessage\FlashMessage;

	class Unite {
		private $coef_unite;


		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$this->coef_unite = Bataille::getParam("coef_niveau_unite");
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//

		/**
		 * @param $unite
		 * @param $niveau
		 * @param $type
		 * @return array
		 * récupère les caractéristiques de l'unité en fonction de son niveau
		 */
		public function getCaracteristiqueUnite($unite, $niveau, $type) {
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select()
				->from("unites")
				->where("nom", "=", $unite, "AND")
				->where("type", "=", $type, "")
				->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) {
					$base_carac = unserialize($obj->caracteristique);
					$ressource = unserialize($obj->pour_recruter);
					$temps_recrutement = DateHeure::Secondeenheure(round($obj->temps_recrutement-($obj->temps_recrutement*Bataille::getBatiment()->getNiveauBatiment("caserne")/100)));
				}

				$coef = $this->coef_unite*$niveau;

				if ($niveau == 1) $coef = 1;

				return [
					"caracteristique" => [
						"attaque" => round($base_carac["attaque"]*$coef),
						"defense" => round($base_carac["defense"]*$coef),
						"resistance" => round($base_carac["resistance"]*$coef),
						"vitesse" => $base_carac["vitesse"]
					],
					"cout_recruter" => [
						"eau" => $ressource["eau"],
						"electricite" => $ressource["electricite"],
						"fer" => $ressource["fer"],
						"fuel" => $ressource["fuel"],
					],
					"temps_recrutement" => $temps_recrutement
				];
			}
			else {
				return [];
			}
		}

		/**
		 * @return array
		 * fonction qui renvoit tous les types d'unités qu'il est possible de recruter
		 */
		private function getAllType() {
			return explode(",", Bataille::getParam("type_unite"));
		}

		/**
		 * @param $type
		 * fonction qui permet de récupérer les unités qu'i est possible de recruter en fonction
		 * du type (batiment sur lequel on a cliqué)
		 */
		public function getUnitePossibleRecruter($type) {
			//on recup toutes les unites deja recherchée donc que l'on peut faire
			$unites = Bataille::getCentreRecherche()->getAllRechercheType($type);

			//recupérer les caractéristiques de l'unité en question
			for ($i=0 ; $i<count($unites) ; $i++) {
				$unites[$i] += $this->getCaracteristiqueUnite($unites[$i]["recherche"], $unites[$i]["niveau"], $type);
				$unites[$i] += ["type" => $type];
			}

			Bataille::setValues(["unites" => $unites]);
		}

		/**
		 * fonction qui renvoi les unité  en cours de recrutement
		 */
		public function getRecrutement() {
			$dbc = App::getDb();

			$query = $dbc->select()->from("_bataille_recrutement")->where("ID_base", "=", Bataille::getIdBase())->get();

			if ((is_array($query)) && (count($query) > 0)) {
				$today = Bataille::getToday();

				foreach ($query as $obj) {
					if ($obj->date_fin-$today <= 0) {
						$this->setTerminerRecrutement($obj->ID_recrutement);
					}
					else {
						$recrutement[] = [
							"nom" => $obj->nom,
							"type" => $obj->type,
							"nombre" => $obj->nombre,
							"date_fin_recrutement" => $obj->date_fin-$today,
							"id_recrutement" => $obj->ID_recrutement
						];
					}
				}

				Bataille::setValues(["recrutement" => $recrutement]);
			}
		}

		/**
		 * @param null $id_base
		 * fonction qui récupère toutes les unités qui sont dans la base
		 */
		public function getAllUnites($id_base = null) {

			if ($id_base == null) $id_base = Bataille::getIdBase();

			$types = $this->getAllType();
			$count_type = count($types);
			$unites = [];

			for ($i=0 ; $i<$count_type ; $i++) {
				$type_unite = $this->getAllUniteType($types[$i], $id_base);

				$unites = array_merge($unites, $type_unite);
			}
			
			Bataille::setValues(["unites" => $unites]);
		}

		/**
		 * @param $type
		 * @param $id_base
		 * @return mixed
		 * fonction qui récupère toutes les unités en fonction d'un type précis
		 */
		private function getAllUniteType($type, $id_base) {
			$dbc = App::getDb();

			$query = $dbc->select("nom")->from("_bataille_unite")
				->where("type", "=", $type, "AND")
				->where("ID_base", "=", $id_base, "AND")
				->where("(ID_groupe IS NULL OR ID_groupe = 0)", "", "", "AND", true)
				->where("(ID_mission IS NULL OR ID_mission = 0)", "", "", "", true)
				->orderBy("nom")
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				$count = 1;
				$nom = "";
				foreach ($query as $obj) {
					if ($nom != $obj->nom) {
						$count = 1;
					}
					$unite[] = $unites[$type][$obj->nom] = [
						"nom" => $obj->nom,
						"nombre" => $count++
					];
					$nom = $obj->nom;
				}

				return $unites;
			}
			
			return [];
		}
		
		/**
		 * @param $type
		 * @param $nom
		 * @return int
		 * renvoi le nombre d'unite en fonction d'un type et d'un nom qui ne sont ni dans un groupe ni
		 * en mission
		 */
		private function getNombreUniteNom($type, $nom) {
			$dbc = App::getDb();
			
			$query = $dbc->select("nom")->from("_bataille_unite")
				->where("type", "=", $type, "AND")
				->where("nom", "=", $nom, "AND")
				->where("ID_base", "=", Bataille::getIdBase(), "AND")
				->where("(ID_groupe IS NULL OR ID_groupe = 0)", "", "", "AND", true)
				->where("(ID_mission IS NULL OR ID_mission = 0)", "", "", "", true)
				->orderBy("nom")
				->get();
			
			return count($query);
		}
		
		/**
		 * @return int
		 * fonction qui renvoi le nombre d'unité vivante dans la base qui consomme de la nourriture
		 */
		public function getNombreUniteHumaine() {
			$dbc = App::getDb();
			
			$query = $dbc->select("ID_unite")->from("_bataille_unite")
				->where("type", "=", "infanterie", "AND")
				->where("ID_base", "=", Bataille::getIdBase())
				->get();
			
			return count($query);
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $nom -> nom de l'unité à recruter
		 * @param $type -> type de l'unité à recruter
		 * @param $nombre -> nombre d'unité à recruter
		 * fonction qui permet d'initialiser le début du recrutement d'unités
		 */
		public function setCommencerRecruter($nom, $type, $nombre) {
			$dbc1 = Bataille::getDb();
			$dbc = App::getDb();

			$query = $dbc1->select("temps_recrutement")
				->select("pour_recruter")
				->from("unites")
				->where("nom", "=", $nom, "AND")
				->where("type", "=", $type, "")
				->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) {
					$pour_recruter = unserialize($obj->pour_recruter);
					$temps_recrutement = round($obj->temps_recrutement-($obj->temps_recrutement*Bataille::getBatiment()->getNiveauBatiment("caserne")/100));
				}
			}

			//on test si on a assez de ressource pour recruter les unites
			//on test si assez de ressources dans la base
			$retirer_eau = intval($pour_recruter["eau"])*$nombre;
			$retirer_electricite = intval($pour_recruter["electricite"])*$nombre;
			$retirer_fer = intval($pour_recruter["fer"])*$nombre;
			$retirer_fuel = intval($pour_recruter["fuel"])*$nombre;
			$eau = Bataille::getTestAssezRessourceBase("eau", $retirer_eau);
			$electricite = Bataille::getTestAssezRessourceBase("electricite", $retirer_electricite);
			$fer = Bataille::getTestAssezRessourceBase("fer", $retirer_fer);
			$fuel = Bataille::getTestAssezRessourceBase("fuel", $retirer_fuel);


			if (($eau["class"] || $electricite["class"] || $fer["class"] || $fuel["class"]) == "rouge" ) {
				FlashMessage::setFlash("Pas assez de ressources pour recruter autant d'unités");
				return false;
			}
			else {
				//on retire les ressources
				Bataille::getRessource()->setUpdateRessource($retirer_eau, $retirer_electricite, $retirer_fer, $retirer_fuel, 0, "-");

				$date_fin = Bataille::getToday()+($temps_recrutement*$nombre);

				$dbc->insert("nom", $nom)
					->insert("type", $type)
					->insert("nombre", $nombre)
					->insert("date_fin", $date_fin)
					->insert("ID_base", Bataille::getIdBase())
					->into("_bataille_recrutement")
					->set();

				return true;
			}
		}

		/**
		 * @param $id_recrutement
		 * fonction appellée dans celle qui récupère les recrutement uniquement quand celui ci est finit
		 * fonction qui sert à terminer un rcrutement et ajouter les unités dans la base
		 */
		private function setTerminerRecrutement($id_recrutement) {
			$dbc = App::getDb();

			$query = $dbc->select()->from("_bataille_recrutement")->where("ID_recrutement", "=", $id_recrutement)->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) {
					$nombre = $obj->nombre;
					$nom = $obj->nom;
					$type = $obj->type;
				}

				for ($i=0 ; $i<$nombre ; $i++) {
					$dbc->insert("nom", $nom)
						->insert("type", $type)
						->insert("ID_base", Bataille::getIdBase())
						->into("_bataille_unite")
						->set();
				}

				$dbc->delete()->from("_bataille_recrutement")->where("ID_recrutement", "=", $id_recrutement)->del();
			}
		}
		
		/**
		 * @param $nombre_unite
		 * @param $nom_unite
		 * @param $type_unite
		 * @param $id_mission
		 * @return bool
		 * permet de lancer des unites en expédition en ajoutant à chaque unité un id_mission
		 */
		public function setCommencerExpedition($nombre_unite, $nom_unite, $type_unite, $id_mission) {
			$dbc = App::getDb();
			
			$nombre_unite_base = $this->getNombreUniteNom($type_unite, $nom_unite);
			
			if ($nombre_unite > $nombre_unite_base) {
				FlashMessage::setFlash("Pas assez d'unités ".$nom_unite." disponibles dans la base pour partir en mission");
				return false;
			}
			
			$dbc->update("ID_mission", $id_mission)
				->from("_bataille_unite")
				->where("type", "=", $type_unite, "AND")
				->where("nom", "=", $nom_unite, "AND")
				->where("ID_base", "=", Bataille::getIdBase())
				->limit($nombre_unite, "no")
				->set();
			
			return true;
		}
		
		/**
		 * @param $id_mission
		 * @param $pourcentage_perte
		 * @return int
		 * fonction qui termine une expdedition au niveau des troupes, cette fonction s'occupe d'en
		 * supprimer de la bdd en fonction du nombre de troupe envoyé et du cpourcentage de perte
		 */
		public function setTerminerExpedition($id_mission, $pourcentage_perte) {
			$dbc = App::getDb();
			$perte = rand(0, $pourcentage_perte);
			
			$query = $dbc->select()->from("_bataille_unite")->where("ID_mission", "=", $id_mission, "AND")
				->where("ID_base", "=", Bataille::getIdBase())
				->get();
			
			//test si il y aura des unités à tuer
			$nombre_unite = count($query);
			$unite_tuees = 0;
			if ((is_array($query)) && ($nombre_unite > 0)) {
				$unite_tuees = round($nombre_unite*$perte/100);
			}
			
			//si oui on en delete aléatoirement
			if ($unite_tuees > 0) {
				$dbc->delete()->from("_bataille_unite")->where("ID_mission", "=", $id_mission, "AND")
					->where("ID_base", "=", Bataille::getIdBase())
					->orderBy("RAND() ")
					->limit($unite_tuees)
					->del();
			}
			
			$dbc->update("ID_mission", 0)
				->from("_bataille_unite")
				->where("ID_base", "=", Bataille::getIdBase(), "AND")
				->where("ID_mission", "=", $id_mission, "", true)
				->set();
			
			//renvoi le nombre d'unites qui ont réussi àrentrer à la base
			return $nombre_unite-$unite_tuees;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}