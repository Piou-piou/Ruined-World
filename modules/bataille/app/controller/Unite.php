<?php
	
	namespace modules\bataille\app\controller;
	
	
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
					"cout_recruter" => $ressource
				];
			}
			else {
				return [];
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
			}

			//si pas d'unites encore recherchees on renvoit un array juste avec 0 dedans
			Bataille::setValues(["unites" => $unites]);
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}