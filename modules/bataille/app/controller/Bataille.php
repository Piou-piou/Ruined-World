<?php
	namespace modules\bataille\app\controller;
	use core\App;
	use core\database\Database;

	class Bataille {
		private static $ressource;
		private static $base;
		private static $batiment;
		private static $database;

		private static $id_base;

		public static $values = [];

		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {

		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public static function getValues() {
		    return ["bataille" => self::$values];
		}

		//initilisation of all classes of battle
		//initialisation of Ressource class
		public static function getRessource() {
			if (self::$ressource == null) {
				self::$ressource = new Ressource();
			}

			return self::$ressource;
		}

		//initialisation of Base class
		public static function getBase() {
			if (self::$base == null) {
				self::$base = new Base();
			}

			return self::$base;
		}

		//initialisation of Batiment class
		public static function getBatiment() {
			if (self::$batiment == null) {
				self::$batiment = new Batiment();
			}

			return self::$batiment;
		}

		//initialisation of Database Core connexion
		public static function getDb() {
			if (self::$database == null) {
				self::$database = new Database("mysql", "bataille_core", "root", "root", "127.0.0.1");
			}
			return self::$database;
		}

		public static function getIdIdentite() {
			return $_SESSION['idlogin'.CLEF_SITE];
		}

		public static function getIdBase() {
			if (self::$id_base == null) {
				self::$id_base = $_SESSION['id_base'];

				return self::$id_base;
			}

			return self::$id_base;
		}

		/**
		 * @return mixed
		 * recupere la date de la derniere connexion
		 */
		public static function getLastConnexion() {
			$dbc = App::getDb();

			$query = $dbc->select()->from("_bataille_last_connexion")->where("ID_identite", "=", self::getIdIdentite())->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					return $obj->last_connexion;
				}
			}

		}

		/**
		 * @return mixed
		 * recupere le nombre maximum d'emplacement dans la base
		 */
		public static function getNombreEmplacementBase() {
			$dbc1 = self::getDb();

			$query = $dbc1->select("nombre_emplacement")->from("configuration")->where("ID_configuration", "=", 1)->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					return $obj->nombre_emplacement;
				}
			}
		}

		/**
		 * @param $nom_ressource
		 * @param $ressource
		 * @return array
		 * fonction qui permet de renvyer la couleur rouge si pas assez de ressource pour construire le batiment
		 * ou pour creer une unité...
		 */
		public static function getTestAssezRessourceBase($nom_ressource, $ressource) {
			$f = "get".ucfirst($nom_ressource);

			if ($ressource >  Bataille::getRessource()->$f()) {
				/*echo("$nom_ressource $ressource ".Bataille::getRessource()->getEau()." ---");*/
				return [
					"ressource" => $ressource,
					"class" => "rouge"
				];
			}

			return [
				"ressource" => $ressource,
				"class" => ""
			];
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * set la date de derniere connexion a now
		 */
		public static function setLastConnexion() {
			$dbc = App::getDb();

			$dbc->update("last_connexion", date("Y-m-d H:i:s"))
				->from("_bataille_last_connexion")
				->where("ID_identite", "=", self::getIdIdentite())
				->set();
		}

		public static function setValues($values) {
			Bataille::$values = array_merge(Bataille::$values, $values);
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}