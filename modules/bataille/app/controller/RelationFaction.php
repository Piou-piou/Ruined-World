<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\HTML\flashmessage\FlashMessage;
	
	class RelationFaction extends Faction {
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * renvoi la liste des relations d'une faction
		 */
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
		public function setAjouterRelation($nom_faction, $relation) {
			$dbc = App::getDb();
			
			if ($this->getFactionExist($nom_faction) == false) {
				FlashMessage::setFlash("Cette faction n'existe pas, vérifiez que vous avez correctement écrit son nom");
				return false;
			}
			
			$dbc->insert("relation", $relation)
				->insert("ID_faction", $this->id_faction)
				->insert("ID_autre_faction", $this->id_autre_faction)
				->into("_bataille_faction_relation")->set();
		}
		
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}