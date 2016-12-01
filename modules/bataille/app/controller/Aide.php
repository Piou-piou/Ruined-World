<?php
	namespace modules\bataille\app\controller;
	
	
	class Aide {
		public static $parametre_router;



		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		/**
		 * recupere la liste des batiments constructibles dans la catégorie spécifiée
		 */
		public function __construct() {
			$dbc1 = Bataille::getDb();

			$parametre = explode("-", self::$parametre_router);
			Bataille::setValues(["batiments" => $parametre[0], "type_batiments" => $parametre[1]]);

			//recuperation de tous les batiments du type
			$query = $dbc1->select()->from($this->getTable($parametre[0]))->where("actif", "=", 1, "AND")->where("type", "=", $parametre[1])->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$nom_batiment = $obj->nom;
					$nom_batiment_sql = $obj->nom_table;

					//on recupere les infos concernant le temps de construction et les batiment qu'il faudra pour construire
					$query = $dbc1->select()->from($obj->nom_table)->where("ID_".$obj->nom_table, "=", 1)->get();

					if ((is_array($query)) && (count($query) == 1)) {
						foreach ($query as $obj) {
							$ressource_tmp = explode(", ", $obj->ressource_construire);

							$ressource = [
								"fer" => $ressource_tmp[0],
								"fuel" => $ressource_tmp[1],
								"eau" => $ressource_tmp[2],
								"electricite" => $ressource_tmp[3]
							];

							if ($obj->pour_construire != null) {
								$pour_construire[] = unserialize($obj->pour_construire);

								$pour_construire = $this->getPourConstruire($pour_construire);
							}
							else {
								$pour_construire = [];
							}

							$batiments[] = [
								"nom_batiment" => $nom_batiment,
								"nom_batiment_sql" => $nom_batiment_sql,
								"niveau_batiment" => 1,
								"temps_construction" => gmdate("H:i:s", $obj->temps_construction),
								"ressource" => $ressource,
								"pour_construire" => $pour_construire
							];
						}
					}
				}

				Bataille::setValues(["liste_batiments" => $batiments]);
			}

			return false;
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * renvoi la table dans laquelle il faut aller chercher les infos
		 * @param $param
		 * @return string
		 */
		private function getTable($param) {
			if ($param == "batiment") {
				return  "liste_batiment";
			}
		}

		/**
		 * @param $pour_construire
		 * @return array
		 * renvoi le ou les batiments nécéssaires pour la construction du batiment spécifié
		 */
		private function getPourConstruire($pour_construire) {
			for ($i=0 ; $i<count($pour_construire) ; $i++) {
				//si plusieur batiment pour construire le batiment en question
				$count = count($pour_construire[$i]);
				if ($count > 1) {
					for ($j=0 ; $j<$count ; $j++) {
						$batiment[] =  [
							"nom_batiment" => $pour_construire[$i][$j][0],
							"niveau_batiment" => $pour_construire[$i][$j][2]
						];
					}

					return ["batiments" => $batiment];
				}
				else {
					$batiment[] =  [
						"nom_batiment" => $pour_construire[$i][0][0],
						"niveau_batiment" => $pour_construire[$i][0][2]
					];

					return ["batiments" => $batiment];
				}
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}