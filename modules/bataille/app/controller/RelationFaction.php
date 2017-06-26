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
		
		/**
		 * fonction qui récupère la liste des relations qu'il sera possible
		 * de mettre dans le select
		 */
		public function getAllRelationsPossible() {
			$dbc1 = Bataille::getDb();
			
			$query = $dbc1->select()->from("faction_relations");
			
			$relations = [];
			foreach ($query as $obj) {
				$relations[] = [
					"relation" => $obj->relation
				];
			}
			
			Bataille::setValues(["liste_relations" => $relations]);
			return $relations;
		}
		
		/**
		 * @param $relation
		 * @return array
		 */
		public function getIdFactionRelation($relation) {
			$dbc = App::getDb();
			
			$query = $dbc->select("ID_autre_faction")->from("_bataille_faction_relation")
				->where("relation", "=", $relation, "AND")
				->where("ID_faction", "=", $this->id_faction)
				->get();
			
			$relations = [];
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$relations[] = $obj->ID_autre_faction;
				}
			}
			
			return $relations;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $nom_faction
		 * @param $relation
		 * @return bool
		 * fonction qui permet d'ajouter une relation
		 */
		public function setAjouterRelation($nom_faction, $relation) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("GERER_RELATIONS", $permissions_membre)) {
				if ($this->getFactionExist($nom_faction) == false) {
					FlashMessage::setFlash("Cette faction n'existe pas, vérifiez que vous avez correctement écrit son nom");
					return false;
				}
				
				$dbc->insert("relation", $relation)
					->insert("ID_faction", $this->id_faction)
					->insert("ID_autre_faction", $this->id_autre_faction)
					->into("_bataille_faction_relation")->set();
			}
			
			FlashMessage::setFlash("Vous n'avez pas l'autorisation de gérer les relations de votre faction");
			return false;
		}
		
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}