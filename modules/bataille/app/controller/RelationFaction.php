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
			$id_faction = [];
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$relations[] = [
						"id_relation" => $obj->ID_faction_relation,
						"relation" => $obj->relation,
						"id_autre_faction" => $obj->ID_autre_faction,
						"nom_autre_faction" => $obj->nom_faction
					];
					
					$id_faction[] = $obj->ID_autre_faction;
				}
			}
			
			Bataille::setValues(["relations" => $relations]);
			
			return $id_faction;
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
				if (in_array($this->id_autre_faction, $this->getListeRelation())) {
					FlashMessage::setFlash("Vous avez déjà une relation avec cette faction");
					return false;
				}
				if ($this->id_autre_faction == $this->id_faction) {
					FlashMessage::setFlash("Vous ne pouvez pas avoir de relations avec votre propre faction");
					return false;
				}
				
				$dbc->insert("relation", $relation)
					->insert("ID_faction", $this->id_faction)
					->insert("ID_autre_faction", $this->id_autre_faction)
					->into("_bataille_faction_relation")->set();
				
				FlashMessage::setFlash("La relation a été ajoutée avec succès", "success");
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas l'autorisation de gérer les relations de votre faction");
			return false;
		}
		
		/**
		 * @param $id_relation
		 * @return bool
		 * fonction qui permet de supprimer une relation
		 */
		public function setSupprimerRelation($id_relation) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("GERER_RELATIONS", $permissions_membre)) {
				$dbc->delete()->from("_bataille_faction_relation")
					->where("ID_faction_relation", "=", $id_relation, "AND")
					->where("ID_faction", "=", $this->id_faction)->del();
				
				FlashMessage::setFlash("La relation a été supprimée avec succès".$id_relation, "success");
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas l'autorisation de gérer les relations de votre faction");
			return false;
		}
		
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}