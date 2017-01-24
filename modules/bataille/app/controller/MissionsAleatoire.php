<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class MissionsAleatoire {
		
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc = App::getDb();
			
			$query = $dbc->select("last_check_mission")->from("_bataille_base")->where("ID_base", "=", Bataille::getIdBase())->get();
			
			if (is_array($query) && (count($query) == 1)) {
				foreach ($query as $obj) {
					$last_check_mission = $obj->last_check_mission;
				}
				
				if ($last_check_mission == "") {
					$this->setUpdateLastCheckMissions();
					$this->setMissionsAleatoire();
				}
				else {
					$today = new \DateTime();
					$last_check_mission = new \DateTime($last_check_mission);
					$interval = $last_check_mission->diff($today);
					
					$diff_jour = explode("+", $interval->format("%R%a"))[1];
					
					if ($diff_jour >= 1) {
						$this->setUpdateLastCheckMissions();
						$this->setMissionsAleatoire();
					}
				}
			}
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * fonction qui récupere tous les types de missions et les return dans un array
		 */
		private function getTypeMission() {
			return explode(",", Bataille::getParam("type_missions"));
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
		
		/**
		 * @param $type
		 * fonction qui recupere des missions aleatoirement de chaque type et qui les ajoute
		 * dans la table _bataille_mission_aleatoire
		 */
		private function setMissionsAleatoire() {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();
			
			$dbc->delete()->from("_bataille_mission_aleatoire")->where("ID_base", "=", Bataille::getIdBase())->del();
			
			$type_missions = $this->getTypeMission();
			
			foreach ($type_missions as $un_type) {
				$query = $dbc1->select()->from("mission")
					->where("type", "=", $un_type)
					->orderBy("RAND()")
					->limit(0, 3)
					->get();
				
				if ((is_array($query)) && (count($query))) {
					foreach ($query as $obj) {
						$dbc->insert("ID_mission", $obj->ID_mission)
							->insert("ID_base", Bataille::getIdBase())
							->into("_bataille_mission_aleatoire")
							->set();
					}
				}
			}
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}