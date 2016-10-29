<?php
	namespace modules\bataille\app\controller;
	use core\App;

	class Base {
		private $batiments;

		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {

		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//



		//-------------------------- GETTER ----------------------------------------------------------------------------//
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

			Bataille::$values = array_merge(Bataille::$values, ["production_eau" => Bataille::getBatiment()->getProduction("eau")]);
			Bataille::$values = array_merge(Bataille::$values, ["production_electricite" => Bataille::getBatiment()->getProduction("electricite")]);
			Bataille::$values = array_merge(Bataille::$values, ["production_fer" => Bataille::getBatiment()->getProduction("fer")]);
			Bataille::$values = array_merge(Bataille::$values, ["production_fuel" => Bataille::getBatiment()->getProduction("fuel")]);
			//Bataille::$values = array_merge(Bataille::$values, ["production_nourriture" => Bataille::getBatiment()->getProduction("nourriture")]);
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
					Bataille::$values = array_merge(Bataille::$values, [
						"nom_base" => $obj->nom_base,
						"points" => $obj->points,
					]);
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
							$batiments[] = [
								"nom_batiment" => $obj->nom_batiment." en construction",
								"nom_batiment_sql" => $obj->nom_batiment_sql,
								"niveau" => $obj->niveau,
								"emplacement" => $i
							];
						}
						else {
							$batiments[] = [
								"nom_batiment" => $obj->nom_batiment,
								"nom_batiment_sql" => $obj->nom_batiment_sql,
								"niveau" => $obj->niveau,
								"emplacement" => $i
							];
						}
					}
				}
				else {
					$batiments[] = [
						"nom_batiment" => "A construire",
						"nom_batiment_sql" => "a_construire",
						"niveau" => 0,
						"emplacement" => $i
					];
				}
			}

			Bataille::$values = array_merge(Bataille::$values, ["batiments" => $batiments]);

			$this->setBatimentsBase($batiments);
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//



		//-------------------------- SETTER ----------------------------------------------------------------------------//
		private function setBatimentsBase($batiments) {
			$this->batiments = $batiments;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}