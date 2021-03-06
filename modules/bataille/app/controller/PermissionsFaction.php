<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\functions\ChaineCaractere;
	use core\HTML\flashmessage\FlashMessage;
	
	class PermissionsFaction {
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		/**
		 * @param $id_identite
		 * @param $id_faction
		 * @return bool
		 * permet de savoir si le joueur en question est le chef de la faction
		 */
		protected function getTestChefFaction($id_identite, $id_faction) {
			$dbc = App::getDb();
			
			$query = $dbc->select("ID_identite")->from("_bataille_faction")
				->where("ID_identite", "=", $id_identite, "AND")
				->where("ID_faction", "=", $id_faction)
				->get();
			
			if (count($query) > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
		 * @return int
		 * fonction qui renvoi le nombre de permissions
		 */
		private function getNombrePermissions() {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_faction_permissions")->get();
			
			return count($query);
		}
		
		/**
		 * @return array
		 * fonction qui liste toutes les permissions
		 */
		public function getListePermissions() {
			$dbc = App::getDb();
			
			$query = $dbc->select()->from("_bataille_faction_permissions")->get();
			
			$permissions = [];
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$permissions[] = str_replace("_", " ", $obj->permission);
				}
			}
			
			Bataille::setValues(["liste_permissions" => $permissions]);
			return $permissions;
		}
		
		/**
		 * @param $id_identite
		 * @param $id_faction
		 * @return array|string
		 * permet de récupérer les permissions d'un membre de la faction
		 */
		protected function getMembrePermissions($id_identite, $id_faction) {
			$dbc = App::getDb();
			
			$nb_permissions = $this->getNombrePermissions();
			
			if ($this->getTestChefFaction($id_identite, $id_faction) === true) {
				$permissions = [];
				for ($i=1 ; $i<=$nb_permissions ; $i++) {
					$permissions[$i] = "checked";
				}
				
				return $permissions;
			}
			
			
			$permissions = [];
			for ($i=1 ; $i<=$nb_permissions ; $i++) {
				$query = $dbc->select()->from("_bataille_faction_permission_player")
					->where("ID_faction", "=", $id_faction, "AND")
					->where("ID_identite", "=", $id_identite, "AND")
					->where("ID_permission", "=", $i)
					->get();
				
				$permissions[$i] = "";
				if (count($query) == 1) {
					$permissions[$i] = "checked";
				}
			}
			
			
			return $permissions;
		}
		
		/**
		 * @param $id_faction
		 * @return bool
		 * fonction qui renvoi les permission du membre connecté
		 */
		public function getPermissionsMembre($id_faction) {
			$dbc = App::getDb();
			
			if ($this->getTestChefFaction(Bataille::getIdIdentite(), $id_faction) == true) {
				Bataille::setValues(["permission_player" => "chef"]);
				return "chef";
			}
			
			$query = $dbc->select()
				->from("_bataille_faction_permission_player")
				->from("_bataille_faction_permissions")
				->where("ID_identite", "=", Bataille::getIdIdentite(), "AND")
				->where("ID_faction", "=", $id_faction, "AND")
				->where("_bataille_faction_permission_player.ID_permission", "=", "_bataille_faction_permissions.ID_permissions", "", true)
				->get();
			
			$permissions = [];
			if ((count($query) > 0)) {
				foreach ($query as $obj) {
					$permissions[] = $obj->permission;
				}
			}
			
			Bataille::setValues(["permission_player" => $permissions]);
			return $permissions;
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $id_permission
		 * @param $id_identite
		 * @param $id_faction
		 * @param $type
		 * cette fonction permet d'ajouter ou de supprimer un permission à un joueur
		 */
		public function setGererPermission($id_permission, $id_identite, $id_faction, $type) {
			$dbc = App::getDb();
			
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("GERER_RANG_MEMBRE", $permissions_membre)) {
				if ($type == "add") {
					$dbc->insert("ID_permission", $id_permission)
						->insert("ID_identite", $id_identite)
						->insert("ID_faction", $id_faction)
						->into("_bataille_faction_permission_player")
						->set();
					FlashMessage::setFlash("La permission a bien été ajoutée", "success");
				}
				else {
					$dbc->delete()->from("_bataille_faction_permission_player")
						->where("ID_faction", "=", $id_faction, "AND")
						->where("ID_permission", "=", $id_permission, "AND")
						->where("ID_identite", "=", $id_identite)
						->del();
					
					FlashMessage::setFlash("La permission a bien été supprimée", "success");
				}
				
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas la permission de gérer les permissions des membres");
			return false;
		}
		
		/**
		 * @param $id_identite
		 * fonction qui va supprimer tous les droits d'accès du user
		 */
		protected function setSupprilerAllPermissions($id_identite) {
			$dbc = App::getDb();
			
			$dbc->delete()->from("_bataille_faction_permission_player")->where("ID_identite", "=", $id_identite)->del();
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}