<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class MissionsAleatoire {
		
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			/*$dbc = App::getDb();
			$dbc1 = Bataille::getDb();*/
			
			//test si on a deje des missions dans la base
			$this->getTestCheckMission();
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui regarde la derniere fois que la récupération de missions a été effectuée.
		 * Si jamais fait on ajoute la date du jour en bdd et on lance la récupération de missions
		 * sinon si elle est supérieur a 1 jour on la remet a la date du jour + on get de nouvelles missions
		 */
		private function getTestCheckMission() {
			$dbc = App::getDb();
			
			$query = $dbc->select("last_check_mission")->from("_bataille_base")->where("ID_base", "=", Bataille::getIdBase())->get();
			
			if (is_array($query) && (count($query) == 1)) {
				foreach ($query as $obj) {
					$last_check_mission = $obj->last_check_mission;
				}
				
				if ($last_check_mission == "") {
					$this->setUpdateLastCheckMissions();
				}
				else {
					$today = new \DateTime();
					$last_check_mission = new \DateTime($last_check_mission);
					$interval = $last_check_mission->diff($today);
					
					$diff_jour = explode("+", $interval->format("%R%a"))[1];
					
					if ($diff_jour >= 1) {
						$this->setUpdateLastCheckMissions();
					}
				}
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui met a jour le last_ckeck_missions dans _bataille_base
		 * le met à la date du jour
		 */
		public function setUpdateLastCheckMissions() {
			$dbc = App::getDb();
			
			$dbc->update("last_check_mission", date("Y-m-d"))
				->from("_bataille_base")
				->where("ID_base", "=", Bataille::getIdBase())
				->set();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}