<?php
	namespace modules\bataille\app\controller;
	
	
	use core\App;
	use core\HTML\flashmessage\FlashMessage;
	use modules\messagerie\app\controller\Messagerie;
	
	class Faction extends PermissionsFaction {
		protected $id_faction;
		protected $id_autre_faction;
		protected $nom_faction;
		
		//-------------------------- BUILDER ----------------------------------------------------------------------------//
		public function __construct() {
			
		}
		//-------------------------- END BUILDER ----------------------------------------------------------------------------//
		
		
		//-------------------------- GETTER ----------------------------------------------------------------------------//
		public function getIdFaction(){
		    return $this->id_faction;
		}
		
		/**
		 * @param $id_faction
		 * @return bool
		 * permet de tester si le joueur est dans la faction affichée
		 */
		private function getTestFactionPlayer($id_faction) {
			$dbc = App::getDb();
			$id_ma_faction = 0;
			
			$query = $dbc->select("ID_faction")->from("_bataille_infos_player")->where("ID_identite", "=", Bataille::getIdIdentite())->get();
			
			foreach ($query as $obj) {
				$id_ma_faction = $obj->ID_faction;
			}
			
			if ($id_ma_faction == $id_faction) {
				Bataille::setValues([
					"ma_faction" => true,
					"id_identite_player" => Bataille::getIdIdentite()
				]);
				return true;
			}
			
			return false;
		}
		
		/**
		 * @return mixed
		 * fonction qui renvoi l'ID de la faction du joueur
		 */
		public function getFactionPlayer($id_identite = null) {
			$dbc = App::getDb();
			
			if ($id_identite === null) {
				$id_identite = Bataille::getIdIdentite();
			}
			
			$query = $dbc->select("ID_faction")->from("_bataille_infos_player")
				->where("ID_identite", "=", $id_identite, "AND")
				->where("ID_faction", ">", 0)
				->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$this->id_faction = $obj->ID_faction;
					$this->getInfosFaction();
				}
				
				return true;
			}
			
			$this->id_faction = "";
			return false;
		}
		
		/**
		 * @param null $id_faction
		 * fonction qui récupère les infos de la faction
		 */
		public function getInfosFaction($id_faction = null) {
			$dbc = App::getDb();
			
			if ($id_faction === null) {
				$id_faction = $this->id_faction;
			}
			
			$this->getTestFactionPlayer($id_faction);
			
			$query = $dbc->select("identite.pseudo")
				->select("_bataille_faction.ID_faction")
				->select("_bataille_faction.nom_faction")
				->select("_bataille_faction.points_faction")
				->select("_bataille_faction.img_profil")
				->select("_bataille_faction.description")
				->from("_bataille_faction")
				->from("identite")
				->where("_bataille_faction.ID_faction", "=", $id_faction, "AND")
				->where("_bataille_faction.ID_identite", "=", "identite.ID_identite", "", true)
				->get();
			
			if ((count($query) == 1)) {
				foreach ($query as $obj) {
					Bataille::setValues(["faction" => [
						"id_faction" => $obj->ID_faction,
						"nom" => $obj->nom_faction,
						"points_faction" => $obj->points_faction,
						"description" => $obj->description,
						"url_img" => $obj->img_profil,
						"pseudo_chef" => $obj->pseudo
					]]);
					
					$this->nom_faction = $obj->nom_faction;
				}
			}
		}
		
		/**
		 * @return array
		 * fonction qui récupère les membres d'un faction
		 */
		public function getMembreFaction() {
			$dbc = App::getDb();
			
			$query = $dbc->select()
				->from("_bataille_infos_player")
				->from("identite")
				->where("_bataille_infos_player.ID_faction", "=", $this->id_faction, "AND")
				->where("_bataille_infos_player.ID_identite", "=", "identite.ID_identite", "", true)
				->orderBy("_bataille_infos_player.points", "DESC")
				->get();
			
			$membre = [];
			$liste_membre = [];
			foreach ($query as $obj) {
				$membre[] = [
					"id_identite" => $obj->ID_identite,
					"pseudo" => $obj->pseudo,
					"points" => $obj->points,
					"rang_faction" => $obj->rang_faction,
					"chef" => $this->getTestChefFaction($obj->ID_identite, $this->id_faction),
					"permissions" => $this->getMembrePermissions($obj->ID_identite, $this->id_faction)
				];
				
				$liste_membre[] = $obj->pseudo;
			}
			
			Bataille::setValues(["membres_faction" => $membre]);
			
			return $liste_membre;
		}
		
		/**
		 * @param $nom_faction
		 * @return bool
		 * ajout d'une fonction pour tester si une faction existe ou non renvoi true si elle existe
		 */
		protected function getFactionExist($nom_faction) {
			$dbc = App::getDb();
			
			$query = $dbc->select("ID_faction")->from("_bataille_faction")->where("nom_faction", "=", $nom_faction)->get();
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$this->id_autre_faction = $obj->ID_faction;
				}
				
				return true;
			}
			
			return false;
		}
		
		/**
		 * foncitons qui renvoit les informations sur les joueurs invités à rejoindre la faction
		 */
		public function getInvitationsEnvoyees() {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			$invitations = [];
			
			if ($permissions_membre == "chef" || in_array("INVITER_MEMBRE", $permissions_membre)) {
				$query = $dbc->select()->from("_bataille_faction_invitation, identite, _bataille_infos_player")
					->where("_bataille_faction_invitation.ID_faction", "=", $this->id_faction, "AND")
					->where("_bataille_faction_invitation.ID_identite", "=", "identite.ID_identite", "AND", true)
					->where("_bataille_faction_invitation.ID_identite", "=", "_bataille_infos_player.ID_identite", "", true)->get();
			}
			
			if (count($query) > 0) {
				foreach ($query as $obj) {
					$invitations[] = [
						"id_identite" => $obj->ID_identite,
						"points" => $obj->points,
						"pseudo" => $obj->pseudo,
						"vacances" => $obj->mode_vacances
					];
				}
			}
			
			Bataille::setValues(["invitations" => $invitations]);
		}
		//-------------------------- END GETTER ----------------------------------------------------------------------------//
		
		
		//-------------------------- SETTER ----------------------------------------------------------------------------//
		/**
		 * @param $id_identite
		 * @return bool
		 * fonction qui permet de renvoyer un membre d'un faction
		 */
		public function setRenvoyerMembre($id_identite) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("RENVOYER_MEMBRE", $permissions_membre)) {
				$dbc->update("ID_faction", 0)
					->update("rang_faction", "")
					->from("_bataille_infos_player")
					->where("ID_identite", "=", $id_identite, "AND")
					->where("ID_faction", "=", $this->id_faction, "", true)
					->set();
				
				$this->setSupprilerAllPermissions($id_identite);
				
				FlashMessage::setFlash("Le membre a bien été renvoyé de la faction", "success");
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas l'autorisation de renvoyer un membre");
			return false;
		}
		
		/**
		 * @param $pseudo
		 * @return bool
		 */
		public function setInviterMembre($pseudo) {
			$dbc = App::getDb();
			$permissions_membre = $this->getPermissionsMembre($this->id_faction);
			
			if ($permissions_membre == "chef" || in_array("INVITER_MEMBRE", $permissions_membre)) {
				$id_identite = Bataille::getPlayerExist($pseudo);
				if ($id_identite== false) {
					FlashMessage::setFlash("Ce joueur n'existe pas");
					return false;
				}
				if (in_array($pseudo, $this->getMembreFaction())) {
					FlashMessage::setFlash("Ce joueur est déjà dans votre faction ou est en attente d'invitation, vous ne pouvez pas l'inviter à nouveau");
					return false;
				}
				
				$infos = [
					"nom_faction" => $this->nom_faction,
					"id_faction" => $this->id_faction
				];
				
				require(MODULEROOT."bataille/app/controller/rapports/invitation-faction.php");
				
				$messagerie = new Messagerie();
				$messagerie->setEnvoyerMessage("Invitation rejoindre faction", $id_identite, $message);
				
				$dbc->insert("ID_faction", $this->id_faction)->insert("ID_identite", $id_identite)
					->into("_bataille_faction_invitation")->set();
				return true;
			}
			
			FlashMessage::setFlash("Vous n'avez pas l'autorisation d'inviter un membre");
			return false;
		}
		//-------------------------- END SETTER ----------------------------------------------------------------------------//    
	}