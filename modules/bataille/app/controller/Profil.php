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
				}
				
				$this->vacances = true;
			}
			else {
				$this->vacances = false;
			}
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
		public function getTestModeVacances() {
			$dbc = App::getDb();
			$vacances = 0;
			$query = $dbc->select("mode_vacances")->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->get();
			
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
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui permet d'activer le mode vacances
		 */
		public static function setActiverModeVacances() {
			$dbc = App::getDb();
			
			$dbc->update("mode_vacances", 1)->update("last_connexion", date("Y-m-d H:i:s"))->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
	}