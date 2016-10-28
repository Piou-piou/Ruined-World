<?php
	namespace modules\bataille\app\controller;
	
	
	class Aide {
		public static $parametre_router;
		public static $batiment;

		private $nom_batiment;
		private $nom_batiment_sql;
		private $niveau_batiment;
		private $temps_construction;
		private $ressource_construire;
		private $nom_batiment_construire;
		private $niveau_batiment_construire;


		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		/**
		 * recupere la liste des batiments constructibles dans la catégorie spécifiée
		 */
		public function __construct() {
			$dbc1 = Bataille::getDb();

			$parametre = explode("-", self::$parametre_router);
			self::$parametre_router = $parametre[1];
			self::$batiment = $parametre[0];

			//recuperation de tous les batiments du type
			$query = $dbc1->select()->from($this->getTable($parametre[0]))->where("actif", "=", 1, "AND")->where("type", "=", $parametre[1])->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$nom_batiment[] = $obj->nom;
					$nom_batiment_sql[] = $obj->nom_table;
					$niveau_batiment[] = 1;
					/*$nom_batiment_construire[] = "";
					$niveau_batiment_construire[] = "";*/

					//on recupere les infos concernant le temps de construction et les batiment qu'il faudra pour construire
					$query = $dbc1->select()->from($obj->nom_table)->where("ID_".$obj->nom_table, "=", 1)->get();

					if ((is_array($query)) && (count($query) > 0)) {
						foreach ($query as $obj) {
							$temps_construction[] = gmdate("H:i:s", $obj->temps_construction);
							$ressource[] = explode(", ", $obj->ressource_construire);

							if ($obj->pour_construire != null) {
								$pour_construire[] = unserialize($obj->pour_construire);
							}
							/*else {
								$pour_construire = [];
							}*/
						}
					}
				}

				for ($i=0 ; $i<count($pour_construire) ; $i++) {
					//si plusieur batiment pour construire le batiment en question
					$count = count($pour_construire[$i]);
					if ($count > 1) {
						for ($j=0 ; $j<$count ; $j++) {
							$temp_nom[] = [$pour_construire[$i][$j][0]];
							$temp_niveau[] = [$pour_construire[$i][$j][2]];
						}

						$nom_batiment_construire[] = $temp_nom;
						$niveau_batiment_construire[] = $temp_niveau;

						$temp_nom = false;
						$temp_niveau = false;
					}
					else {
						$nom_batiment_construire[] = $pour_construire[$i][0][0];
						$niveau_batiment_construire[] = $pour_construire[$i][0][2];
					}
				}

				$this->setListeBatiment($nom_batiment, $nom_batiment_sql, $niveau_batiment, $temps_construction, $ressource, $nom_batiment_construire, $niveau_batiment_construire);
			}

			return false;
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getNomBatiment(){
		    return $this->nom_batiment;
		}
		public function getNomBatimentSql(){
		    return $this->nom_batiment_sql;
		}
		public function getNiveauBatiment(){
		    return $this->niveau_batiment;
		}
		public function getTempsConstruction(){
		    return $this->temps_construction;
		}
		public function getRessourceConstruire(){
		    return $this->ressource_construire;
		}
		public function getNomBatimentConstruire(){
		    return $this->nom_batiment_construire;
		}
		public function getNiveauBatimentConstruire(){
		    return $this->niveau_batiment_construire;
		}

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
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		private function setListeBatiment($nom_batiment, $nom_batiment_sql, $niveau_batiment, $temps_construction, $ressource, $nom_batiment_construire, $niveau_batiment_construire) {
			$this->nom_batiment = $nom_batiment;
			$this->nom_batiment_sql = $nom_batiment_sql;
			$this->niveau_batiment = $niveau_batiment;
			$this->temps_construction = $temps_construction;
			$this->ressource_construire = $ressource;
			$this->nom_batiment_construire = $nom_batiment_construire;
			$this->niveau_batiment_construire = $niveau_batiment_construire;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}