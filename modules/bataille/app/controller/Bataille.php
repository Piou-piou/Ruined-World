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
		/**
		 * @return array
		 * get array of all values wich will be used in the page
		 */
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
				self::$database = new Database("mysql", "bataille_core", "root", "Gerto80", "127.0.0.1");
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
		 * @param null $id_base -> sert si definit a recuperer l'id identite de la abse en question
		 * @return mixed
		 * recupere la date de la derniere connexion
		 */
		public static function getLastConnexion($id_base = null) {
			$dbc = App::getDb();

			if ($id_base === null) {
				$query = $dbc->select()->from("_bataille_last_connexion")->where("ID_identite", "=", self::getIdIdentite())->get();
			}
			else {
				$query = $dbc->select("_bataille_last_connexion.last_connexion")->from("_bataille_base")
					->from("_bataille_last_connexion")
					->from("identite")
					->where("_bataille_base.ID_base", "=", $id_base, "AND")
					->where("_bataille_base.ID_identite", "=", "identite.ID_identite", "AND", true)
					->where("identite.ID_identite", "=", "_bataille_last_connexion.ID_identite", "", true)
					->get();
			}


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
		
		/**
		 * @param $id_base
		 * @return number
		 * fonction qui renvoi le temps de trajet entre la base du joueur et une autre base
		 */
		public static function getDureeTrajet($id_base) {
			//récupération de la posisiotn de la base du joueur + la base sur laquelle on a cliqué
			$base_joueur = self::getPosistionBase($_SESSION['id_base']);
			$base_autre = self::getPosistionBase($id_base);
			
			//calcul des distances séparant les deux bases en x et y
			//cette dstance sera multipliée par 10 sur x et y puis ajoutée pour avoir le temps du trajte en seconde
			$calc_x = abs($base_joueur['posx']-$base_autre['posx']);
			$calc_y = abs($base_joueur['posy']-$base_autre['posy']);
			
			$temps_voyage = ($calc_x*15)+($calc_y*15);
			
			return $temps_voyage;
		}
		
		/**
		 * @param $id_base
		 * @return array
		 * fonction qui renvoi les posisitons en x et y d'une base
		 */
		private static function getPosistionBase($id_base) {
			$dbc = App::getDb();
			
			$query = $dbc->select("posx")
				->select("posy")
				->from("_bataille_base")
				->where("ID_base", "=", $id_base)
				->get();
			
			foreach ($query as $obj) {
				$posx = $obj->posx;
				$posy = $obj->posy;
			}
			
			return ["posx" => $posx, "posy" => $posy];
		}

		/**
		 * @param null $id_identite
		 * get nation of a player
		 */
		public static function getNationBase($id_identite = null) {
			$dbc = App::getDb();

			if ($id_identite === null) {
				$id_identite = $_SESSION['idlogin'.CLEF_SITE];
			}

			$query = $dbc->select("nation")
				->from("identite")
				->from("_bataille_nation")
				->where("identite.ID_identite", "=", $id_identite, "AND")
				->where("identite.ID_identite", "=", "_bataille_nation.ID_identite", "", true)
				->get();

			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					self::setValues(["nation" => $obj->nation]);
				}
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * set la date de derniere connexion a now
		 */
		public static function setLastConnexion($id_base = null) {
			$dbc = App::getDb();

			if ($id_base === null) {
				$id_identite = self::getIdIdentite();
			}
			else {
				$query = $dbc->select("ID_identite")->from("_bataille_base")->where("ID_base", "=", $id_base)->get();

				foreach ($query as $obj) $id_identite = $obj->ID_identite;
			}

			$dbc->update("last_connexion", date("Y-m-d H:i:s"))
				->from("_bataille_last_connexion")
				->where("ID_identite", "=", $id_identite)
				->set();
		}

		/**
		 * @param $values
		 * can set values while keep older infos
		 */
		public static function setValues($values) {
			Bataille::$values = array_merge(Bataille::$values, $values);
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}