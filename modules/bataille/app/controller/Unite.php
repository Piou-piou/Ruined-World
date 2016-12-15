<?php
	
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\functions\DateHeure;
	use core\HTML\flashmessage\FlashMessage;

	class Unite {
		private $coef_unite;


		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select("coef_niveau_unite")->from("configuration")->where("ID_configuration", "=", 1)->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) $this->coef_unite = $obj->coef_niveau_unite;
			}
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
		private function getCaracteristiqueUnite($unite, $niveau, $type) {
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
					$temps_recrutement = DateHeure::Secondeenheure($obj->temps_recrutement);
				}

				$coef = $this->coef_unite*$niveau;
				$coef_ameliorer = $this->coef_unite*($niveau+1);

				if ($niveau == 1) $coef = 1;

				return [
					"caracteristique" => [
						"attaque" => round($base_carac["attaque"]*$coef),
						"defense" => round($base_carac["defense"]*$coef),
						"resistance" => round($base_carac["resistance"]*$coef),
						"vitesse" => $base_carac["vitesse"]
					],
					"cout_recruter" => [
						"eau" => $ressource["eau"]*$coef,
						"electricite" => $ressource["electricite"]*$coef,
						"fer" => $ressource["fer"]*$coef,
						"fuel" => $ressource["fuel"]*$coef,
					],
					"cout_ameliorer" => [
						"eau" => $ressource["eau"]*$coef_ameliorer,
						"electricite" => $ressource["electricite"]*$coef_ameliorer,
						"fer" => $ressource["fer"]*$coef_ameliorer,
						"fuel" => $ressource["fuel"]*$coef_ameliorer,
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
			$dbc1 = Bataille::getDb();

			$query = $dbc1->select("type_unite")->from("configuration")->where("ID_configuration", "=", 1)->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) return explode(",", $obj->type_unite);
			}
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

			//si pas d'unites encore recherchees on renvoit un array juste avec 0 dedans
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
				//$unite_vehicule = $this->getAllUniteType("véhicule", $id_base);

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
		public function getAllUniteType($type, $id_base) {
			$dbc = App::getDb();

			$query = $dbc->select("nom")->from("_bataille_unite")
				->where("type", "=", $type, "AND")
				->where("ID_base", "=", $id_base, "AND")
				->where("(ID_groupe IS NULL OR ID_groupe = 0)", "", "", "", true)
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
					$temps_recrutement = $obj->temps_recrutement;
				}
			}

			//on test si on a assez de ressource pour recruter les unites
			//on test si assez de ressources dans la base
			$retirer_eau = $pour_recruter["eau"]*$nombre;
			$retirer_electricite = $pour_recruter["electricite"]*$nombre;
			$retirer_fer = $pour_recruter["fer"]*$nombre;
			$retirer_fuel = $pour_recruter["fuel"]*$nombre;
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

				if ($type == "unité infanterie") $table = "_bataille_unite";

				for ($i=0 ; $i<$nombre ; $i++) {
					$dbc->insert("nom", $type)
						->insert("type", $nom)
						->insert("ID_base", Bataille::getIdBase())
						->into($table)
						->set();
				}

				$dbc->delete()->from("_bataille_recrutement")->where("ID_recrutement", "=", $id_recrutement)->del();
			}
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}