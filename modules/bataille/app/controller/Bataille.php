<?php
	namespace modules\bataille\app\controller;
	use core\App;

	class Bataille extends InitialiseClass {
		private static $nation;

		private static $id_base;

		public static $values = [];

		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * @return array
		 * get array of all values wich will be used in the page
		 */
		public static function getValues() {
			return ["bataille" => self::$values];
		}
		
		/**
		 * @return mixe
		 * récupère l'ID_identité du joueur
		 */
		public static function getIdIdentite() {
			return $_SESSION['idlogin'.CLEF_SITE];
		}

		/**
		 * @return mixed
		 * renvoi l'id_base du joueur
		 */
		public static function getIdBase() {
			if (self::$id_base == null) {
				self::$id_base = $_SESSION['id_base'];

				return self::$id_base;
			}

			return self::$id_base;
		}

		/**
		 * @return mixed
		 * renvoi le premier ID_base du joueur (première base et base princ du joueur)
		 */
		public static function getFirstBase() {
			$dbc = App::getDb();

			$query = $dbc->select("ID_base")->from("_bataille_base")
				->where("ID_identite", "=", self::getIdIdentite())
				->orderBy("ID_base")
				->limit(0, 1)
				->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) return $obj->ID_base;
			}
		}

		/**
		 * @param $id_base
		 * @return array
		 * fonction qui renvoi les posisitons en x et y d'une base
		 */
		private static function getPosistionBase($id_base) {
			$dbc = App::getDb();
			
			$posx = 0;
			$posy = 0;

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
		 * @return int
		 * return now timestamp
		 */
		public static function getToday() {
			$today = new \DateTime();
			return $today->getTimestamp();
		}

		/**
		 * @param string $nom_ressource
		 * @param $ressource
		 * @return array
		 * fonction qui permet de renvyer la couleur rouge si pas assez de ressource pour construire le batiment
		 * ou pour creer une unité...
		 */
		public static function getTestAssezRessourceBase($nom_ressource, $ressource) {
			$f = "get".ucfirst($nom_ressource);

			if ($ressource > Bataille::getRessource()->$f()) {
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
		 * @param integer $vitesse = vitesse de l'unité en question
		 * @return number
		 * fonction qui renvoi le temps de trajet entre la base du joueur et une autre base en secondes
		 */
		public static function getDureeTrajet($id_base, $vitesse = 1) {
			//récupération de la posisiotn de la base du joueur + la base sur laquelle on a cliqué
			$base_joueur = self::getPosistionBase($_SESSION['id_base']);
			$base_autre = self::getPosistionBase($id_base);
			
			//calcul des distances séparant les deux bases en x et y
			//cette dstance sera multipliée par 15 sur x et y puis ajoutée pour avoir le temps du trajte en seconde
			$calc_x = abs($base_joueur['posx']-$base_autre['posx']);
			$calc_y = abs($base_joueur['posy']-$base_autre['posy']);
			
			$temps_voyage = (($calc_x*70)+($calc_y*70))/$vitesse;
			
			return $temps_voyage;
		}

		/**
		 * @param null $id_identite
		 * get nation of a player
		 */
		public static function getNation($id_identite = null) {
			$dbc = App::getDb();

			if (($id_identite === null) && (self::$nation == null)) {
				$id_identite = Bataille::getIdIdentite();
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

		/**
		 * @param $posx
		 * @param $posy
		 * @return int
		 * fonction qui renvoi un ID_base en fonction de sa posx et posy et 0 si base inexistante
		 */
		public static function getBaseExistPosition($posx, $posy) {
			$dbc = App::getDb();

			$query = $dbc->select("ID_base")->from("_bataille_base")
				->where("posx", "=", $posx, "AND")
				->where("posy", "=", $posy)
				->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) return $obj->ID_base;
			}

			return 0;
		}

		/**
		 * @param string $param
		 * @return mixed
		 * fonction qui sert à récupérer un parametre spécifique pour un batiment
		 * par exemple la vitesse d'un marchand ou  le nombred'emplacment de la base
		 */
		public static function getParam($param) {
			$dbc = self::getDb();

			$query = $dbc->select($param)->from("configuration")->where("ID_configuration", "=", 1)->get();

			if ((is_array($query)) && (count($query) == 1)) {
				foreach ($query as $obj) return $obj->$param;
			}
		}
		
		/**
		 * @return mixed
		 * fonction qui renvoi la date de dernière connexion d'un joueur
		 */
		public static function getLastConnexionPlayer() {
			$dbc = App::getDb();
			
			$query = $dbc->select("last_connexion")->from("_bataille_infos_player")
				->where("ID_identite", "=", self::getIdIdentite())
				->get();
			
			if ((is_array($query)) && (count($query) > 0)) {
				foreach ($query as $obj) {
					return $obj->last_connexion;
				}
			}
		}
		
		/**
		 * @param $pseudo
		 * @return bool
		 * fonction qui renvoi l'ID_identite d'un joueur si il existe
		 */
		public static function getPlayerExist($pseudo) {
			$dbc = App::getDb();
			
			$pseudo = trim($pseudo);
			
			$query = $dbc->select("identite.ID_identite")->from("identite, _bataille_infos_player")
				->where("identite.pseudo", "=", $pseudo, "AND")
				->where("_bataille_infos_player.abandon", "=", 0, "AND")
				->where("identite.ID_identite", "=", "_bataille_infos_player.ID_identite", "", true)->get();
			
			if (count($query) == 1) {
				foreach ($query as $obj) {
					return $obj->ID_identite;
				}
			}
			
			return false;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $values
		 * can set values while keep older infos
		 */
		public static function setValues($values) {
			Bataille::$values = array_merge(Bataille::$values, $values);
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}