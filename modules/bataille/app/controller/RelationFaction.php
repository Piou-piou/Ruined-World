<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	
	class RelationFaction extends Faction {
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getListeRelation() {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_faction_relation")
				->from("_bataille_faction")
				->where("_bataille_faction_relation.ID_faction", "=", $this->id_faction, "AND")
				->where("_bataille_faction_relation.ID_autre_faction", "=", "_bataille_faction.ID_faction", "", true)
				->get();
			
			$relations = [];
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$relations[] = [
						"id_relation" => $obj->ID_faction_relation,
						"relation" => $obj->relation,
						"id_autre_faction" => $obj->ID_autre_faction,
						"nom_autre_faction" => $obj->nom_faction
					];
				}
			}
			
			Bataille::setValues(["relations" => $relations]);
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}