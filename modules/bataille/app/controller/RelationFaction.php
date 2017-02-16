<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class RelationFaction extends Faction {
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getListeRelation() {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_faction_relation")->where("ID_faction", "=", $this->id_faction)->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					
				}
			}
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}