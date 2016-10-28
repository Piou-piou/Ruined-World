<?php
	namespace modules\bataille\app\controller;
	use core\App;

	class Base {
		private $nom_base;
		private $points;
		private $batiments;

		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {

		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getNomBase() {
			return $this->nom_base;
		}
		public function getPoints() {
			return $this->points;
		}
		public function getBatiments(){
		    return $this->batiments;
		}

		/**
		 * fonction qui va initaliser la base
		 */
		public function getMaBase() {
			$this->getInfoBase();

			Bataille::getRessource();

			$this->getBatimentsBase();
		}

		/**
		 * @param null $id_base
		 * fonction qui va récupérer les infos de la base
		 */
		public function getInfoBase($id_base = null) {
			$dbc = App::getDb();

			if ($id_base == null) {
				$id_base = Bataille::getIdBase();
			}

			$query = $dbc->select("nom_base")->select("points")->from("_bataille_base")->where("ID_base", "=", $id_base)->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					$this->nom_base = $obj->nom_base;
					$this->points = $obj->points;
				}
			}
		}

		/**
		 * fonction qui recupere tous les batiments de la base
		 */
		public function getBatimentsBase() {
			$dbc = App::getDb();

			$nombre_emplacement = Bataille::getNombreEmplacementBase();

			for ($i=1 ; $i<($nombre_emplacement+1) ; $i++) {
				$query = $dbc->select()->from("_bataille_batiment")->where("ID_base", "=", Bataille::getIdBase(), "AND")
					->where("emplacement", "=", $i)
					->orderBy("emplacement")
					->get();

				if (count($query) == 1) {
					foreach ($query as $obj) {
						if ($obj->construction) {
							$batiments[] = [$obj->nom_batiment." en construction", $obj->nom_batiment_sql, $obj->niveau, $i];
						}
						else {
							$batiments[] = [$obj->nom_batiment, $obj->nom_batiment_sql, $obj->niveau, $i];
						}
					}
				}
				else {
					$batiments[] = ["A construire", "a_construire", 0, $i];
				}
			}

			$this->setBatimentsBase($batiments);
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		private function setBatimentsBase($batiments) {
			$this->batiments = $batiments;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}