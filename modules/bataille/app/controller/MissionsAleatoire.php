<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class MissionsAleatoire {
		
		
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			$dbc = App::getDb();
			$dbc1 = Bataille::getDb();
			
			//test si on a deje des missions dans la base
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		private function getTestCheckMission() {
			$dbc = App::getDb();
			
			$query = $dbc->select("last_check_mission")->from("_bataille_base")->where("ID_base", "=", Bataille::getIdBase())->get();
			
			if (is_array($query) && (count($query) == 1)) {
				foreach ($query as $obj) {
					$last_check_mission = $obj->last_check_mission;
				}
				
				$today = new \DateTime();
				
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//
		
	}