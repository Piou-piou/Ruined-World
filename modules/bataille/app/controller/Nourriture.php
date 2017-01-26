<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class Nourriture {
		
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$last_check = $this->getLastCheckNourriture();
			$nb_unite = Bataille::getUnite()->getNombreUniteHumaine();
			
			if ($last_check >= 3600) {
				echo("a re-check");
			}
			else if (($last_check == 0) && ($nb_unite > 0)) {
				$this->setUpdateLastCheckNourriture();
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * @return int
		 * fonction qui renvoi la durée écoulée depuis le dernier check de la nourriture
		 */
		private function getLastCheckNourriture() {
			$dbc= App::getDb();
			
			$query = $dbc->select("last_check_nourriture")->from("_bataille_last_connexion")->where("ID_identite", "=", Bataille::getIdIdentite())->get();
			
			if ((is_array($query)) && (count($query) == 1)) {
				$today = Bataille::getToday();
				
				foreach ($query as $obj) {
					$last_check = $obj->last_check_nourriture;
				}
				
				$last_co = new \DateTime($last_check);
				$last_co = $last_co->getTimestamp();
				
				return $today-$last_co;
			}
			
			return 0;
		}
		
		private function getConsommationNourritureUnite() {
			return  Bataille::getParam("unite_nourriture_heure");
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * remet la date de last_check_nourriture a Y-m-d H:i:s
		 */
		private function setUpdateLastCheckNourriture() {
			$dbc = App::getDb();
			
			$dbc->update("last_check_nourriture", date("Y-m-d H:i:s"))->from("_bataille_last_connexion")->where("ID_identite", "=", Bataille::getIdIdentite())->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}