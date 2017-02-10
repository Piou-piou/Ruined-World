<?php
	namespace modules\bataille\app\controller;
	
	use core\database\Database;
	
	class InitialiseClass {
		protected static $ressource;
		protected static $base;
		protected static $batiment;
		protected static $unite;
		protected static $groupe_unite;
		protected static $centre_recherche;
		protected static $missions_aleatoire;
		protected static $database;
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		
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
		
		//initialisation of Unite class
		public static function getUnite() {
			if (self::$unite == null) {
				self::$unite = new Unite();
			}
			
			return self::$unite;
		}
		
		//initialisation of GroupeUnite class
		public static function getGoupeUnite() {
			if (self::$groupe_unite == null) {
				self::$groupe_unite = new GroupeUnite();
			}
			
			return self::$groupe_unite;
		}
		
		//initialisation of CentreRecherche class
		public static function getCentreRecherche() {
			if (self::$centre_recherche == null) {
				self::$centre_recherche = new CentreRecherche();
			}
			
			return self::$centre_recherche;
		}
		
		//initialisation of MissionsAleatoire class
		public static function getMissionsAleatoire() {
			if (self::$missions_aleatoire == null) {
				self::$missions_aleatoire = new MissionsAleatoire();
			}
			
			return self::$missions_aleatoire;
		}
		
		//initialisation of Database Core connexion
		public static function getDb() {
			require_once("config.config.php");
			
			if (self::$database == null) {
				self::$database = new Database($type_co, $dbname, $dbuser, $dbpass, $dbhost);
			}
			return self::$database;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}