<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\HTML\flashmessage\FlashMessage;
	
	class Profil {
		private $vacances;
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			if ($this->getTestModeVacances() == 1) {
				if ($this->getDureeVacances() < 2) {
					FlashMessage::setFlash("Vous ne pouvez pas vous reconnecter sur votre compte car le mode vacances n'est pas actif depuis plus de 48h !");
					$this->vacances = "<48";
					return;
				}
				
				$this->vacances = ">48";
				return;
				
			}
			
			$this->vacances = false;
			return;
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getVacances(){
		    return $this->vacances;
		}
		
		/**
		 * @return string
		 * fonction qui test si le compte est en mode vacances ou pas
		 */
		public function getTestModeVacances($id_identite = null) {
			$dbc = App::getDb();
			$vacances = 0;
			
			if ($id_identite === null) {
				$id_identite = Bataille::getIdIdentite();
			}
			
			$query = $dbc->select("mode_vacances")->from("_bataille_infos_player")->where("ID_identite", "=", $id_identite)->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$vacances = $obj->mode_vacances;
				}
			}
			
			if ($vacances > 0) {
				return 1;
			}
			
			 return 0;
		}
		
		/**
		 * @return string
		 * fonction qui renvoi la durée depuis lquelle le compte est en vacances
		 */
		public function getDureeVacances() {
			$last_connexion = new \DateTime(Bataille::getLastConnexionPlayer());
			$today = new \DateTime();
			
			$interval = $today->diff($last_connexion);
			
			return $interval->format('%a');
		}
		
		/**
		 * @param $id_base
		 * @return mixed
		 * permet de tester si une base est en vacances ou non
		 */
		public static function getTestVacancesBase($id_base) {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_base")->from("_bataille_infos_player")
				->where("_bataille_base.ID_base", "=", $id_base, "AND")
				->where("_bataille_base.ID_identite", "=", "_bataille_infos_player.ID_identite", "", true)->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					return $obj->mode_vacances;
				}
			}
			
			return 0;
		}
		
		/**
		 * @return bool
		 * fonction qui récupère toutes les construction en cours de la base
		 */
		private function getAllConstructionBases() {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_base")->from("_bataille_batiment")
				->where("_bataille_base.ID_identite", "=", Bataille::getIdIdentite(), "AND")
				->where("_bataille_batiment.construction", "=", 1, "AND")
				->where("_bataille_base.ID_base", "=", "_bataille_batiment.ID_base", "", true)->get();
			
			if (count($query) > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
		 * @return bool
		 * fonction qui récupère toutes les recherches, recrutement, missions et offre de marché en cours de la base
		 */
		private static function getAllThingsBases($table) {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_base")->from($table)
				->where("_bataille_base.ID_identite", "=", Bataille::getIdIdentite(), "AND")
				->where("_bataille_base.ID_base", "=", $table.".ID_base", "", true)->get();
			
			if (count($query) > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
		 * @return bool
		 * fonction qui récupère toutes les transports en cours de la base
		 */
		private static function getAllMarcheBases() {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_base")->from("_bataille_marche_transport")
				->where("_bataille_base.ID_identite", "=", Bataille::getIdIdentite(), "AND")
				->where("_bataille_base.ID_base", "=", "_bataille_marche_transport.ID_base", "OR", true)->get()
				->where("_bataille_base.ID_base", "=", "_bataille_marche_transport.ID_base_dest", "", true)->get();
			
			if (count($query) > 0) {
				return true;
			}
			
			return false;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui permet d'activer le mode vacances
		 */
		public static function setActiverModeVacances() {
			$dbc = App::getDb();
			
			if ((self::getAllConstructionBases() == false) && (self::getAllMarcheBases() == false) &&
				(self::getAllThingsBases("_bataille_marche_transport") == false) &&
				(self::getAllThingsBases("_bataille_marche_recrutement") == false) &&
				(self::getAllThingsBases("_bataille_missions_cours") == false) &&
				(self::getAllThingsBases("_bataille_marche_recherche") == false)) {
				
				$dbc->update("mode_vacances", 1)->update("last_connexion", date("Y-m-d H:i:s"))->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
				FlashMessage::setFlash("Le mode vacances a bien été activé");
				return true;
			}
			
			FlashMessage::setFlash("impossible de passer en mode vacances des actions sont encore en cours dans vos bases, merci d'en faire le tour pour vérifier");
			return false;
		}
		
		/**
		 * fonction qui permet de finir le mode vacances
		 */
		public function setDesactiverModeVacances() {
			$dbc = App::getDb();
			
			$dbc->update("mode_vacances", 0)->update("last_connexion", date("Y-m-d H:i:s"))->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
		
			$dbc->update("last_connexion", date("Y-m-d- H:i:s"))
				->update("last_check_nourriture", date("Y-m-d- H:i:s"))
				->from("_bataille_base")
				->where("ID_identite", "=", Bataille::getIdIdentite())->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}